<?php

namespace App\Http\Responses\Customer;

use App\Models\Item;
use App\Models\Size;
use App\Models\Color;
use App\Models\Category;
use App\Models\Material;
use Illuminate\Support\Str;
use App\Models\CustomerItem;
use App\Models\CustomerItemPrice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Support\Responsable;

class StoreManageProductResponse implements Responsable
{
    protected $itemData;

    public function toResponse($request)
    {
        $this->getDataItems($request);
        $items = Item::select('id')->get()->implode('id', ',');
        $categories = Category::select('id')->get()->implode('id', ',');
        $materials = Material::select('id')->get()->implode('id', ',');
        $colors = Color::select('id')->get()->implode('id', ',');

        $validator = Validator::make($this->itemData, [
            'items.*.item_id' => 'required|in:' . $items,
            'items.*.item_combination' => 'required|distinct',
            'items.*.price' => 'required|numeric',
            'items.*.special_price' => 'required|numeric',
            'items.*.type' => 'required|in:' . $categories,
            'items.*.material_id' => 'required|in:' . $materials,
            'items.*.color_id' => 'required|in:' . $colors,
            'items.*.note' => 'sometimes|max:225',
            'items.*.special_note' => 'sometimes|max:225',
            'items.*.screen_printing' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $this->storeData($request);

        return response()->json([
            'status' => true,
            'message' => 'Data save.',
        ]);
    }

    protected function getDataItems($request)
    {
        $data = collect($request->input('items.*.data'))->map(function ($item, $key) {
            $itemData = json_decode($item, true);
            return [
                'item_id' => $itemData['items.' . $key . '.item_id'],
                'item_combination' => $itemData['items.' . $key . '.item_combination'],
                'unit' => $itemData['items.' . $key . '.unit'],
                'type' => $itemData['items.' . $key . '.type'],
                'price' => $itemData['items.' . $key . '.price'],
                'special_price' => $itemData['items.' . $key . '.special_price'],
                'material_id' => $itemData['items.' . $key . '.material_id'],
                'color_id' => $itemData['items.' . $key . '.color_id'],
                'note' => $itemData['items.' . $key . '.note'],
                'special_note' => $itemData['items.' . $key . '.special_note'],
                'screen_printing' => $itemData['items.' . $key . '.screen_printing'],
            ];
        });

        $this->itemData = [
            'items' => $data->toArray()
        ];
    }

    protected function storeData($request)
    {
        DB::transaction(function () use ($request) {
            $customerItems = CustomerItem::where('customer_id', $request->customer_id)->paginate(50);
            $dataIds = collect($customerItems->items())->pluck('id');
            CustomerItemPrice::whereIn('customer_item_id', $dataIds)->forceDelete();

            CustomerItem::whereIn('id', $dataIds)->forceDelete();
            $this->saveCustomerItem($request);
        });
    }

    protected function saveCustomerItem($request)
    {
        foreach ($this->itemData['items'] as $key => $item) {
            $file = $request->file("items.{$key}.image");
            $imageName = $file ?: '';
            
            if ($file) {
                $upload = $this->storeImage($file);
                $imageName = $upload['name'];
            }

            $customerItem = CustomerItem::create([
                'customer_id' => $request->customer_id,
                'item_id' => $item['item_id'],
                'material_id' => $item['material_id'],
                'color_id' => $item['color_id'],
                'image' => $imageName,
                'note' => $item['note'] ?: '',
                'special_note' => $item['special_note'] ?: '',
                'screen_printing' => $item['screen_printing'],
            ]);

            $this->saveCustomerItemPrices($customerItem, $item['price'], $item['special_price']);
        }
    }

    protected function storeImage($image)
    {
        $fileName = time() . Str::random(9) . '.' . $image->extension();
        $path = Storage::putFileAs(
            'orders',
            $image,
            $fileName
        );

        return [
            'name' => $fileName,
            'path' => $path,
        ];
    }

    protected function saveCustomerItemPrices($customerItem, $price, $specialPrice)
    {
        $priceData = [];
        foreach (Size::orderBy('name')->get() as $size) {
            $priceData[] = [
                'customer_item_id' => $customerItem->id,
                'size_id' => $size->id,
                'price' => $price,
                'special_price' => $specialPrice,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        CustomerItemPrice::insert($priceData);
    }
}
