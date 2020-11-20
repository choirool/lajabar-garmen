<?php

namespace App\Http\Responses\Customer;

use App\Models\Size;
use Illuminate\Support\Str;
use App\Models\CustomerItem;
use App\Models\CustomerItemPrice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Support\Responsable;

class StoreManageProductResponse implements Responsable
{
    public function toResponse($request)
    {
        $this->storeData($request);

        return response()->json([
            'status' => true,
            'message' => 'Data save.',
        ]);
    }

    protected function storeData($request)
    {
        DB::transaction(function () use ($request) {
            CustomerItemPrice::whereHas('customerItem', function ($query) use ($request) {
                $query->where('customer_id', $request->customer_id);
            })->forceDelete();

            CustomerItem::where('customer_id', $request->customer_id)->forceDelete();
            $this->saveCustomerItem($request);
        });
    }

    protected function saveCustomerItem($request)
    {
        foreach ($request->items as $item) {
            $imageName = $item['image'] ?: '';

            if ($item['image'] && get_class($item['image']) == 'Illuminate\Http\UploadedFile') {
                $upload = $this->storeImage($item['image']);
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
