<?php

namespace App\Http\Responses\Production;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Production;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Support\Responsable;

class ProductionStoreResponse implements Responsable
{
    protected $productions;

    public function __construct($orderId)
    {
        $this->productions = $this->productions($orderId);
    }

    public function toResponse($request)
    {
        DB::transaction(function () use ($request) {
            $this->saveData($request);
        });

        session()->flash('message', 'Data successfully created.');

        return response()->json([
            'status' => true,
            'message' => 'Data successfully saved.'
        ]);
    }

    protected function saveData($request)
    {
        $data = [];

        foreach ($request->all() as $value) {
            $this->updateOrderItem($value);

            foreach ($value['values'] as $v) {
                if ($v['id'] && $v['value'] > 0) {
                    $currentProduction = $this->getCurrentProduction($v);
                    $totalCurrentProduction = $currentProduction->sum('value');
                    $insertValue = (int) $v['value'];

                    if ($currentProduction->count()) {
                        $insertValue = (int) $v['value'] - $totalCurrentProduction;
                    }

                    if ($insertValue !== 0) {
                        $data[] = [
                            'order_item_price_id' => $v['id'],
                            'value' => $insertValue,
                            'size_id' => $v['size_id'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
            }
        }

        Production::insert($data);
    }

    protected function updateOrderItem($data)
    {
        if (auth()->user()->isAbleTo('order-update-status')) {
            OrderItem::where('id', $data['id'])
                ->update([
                    'status_id' => $data['status'],
                ]);
        }
    }

    protected function productions($orderId)
    {
        return Production::query()
            ->whereHas('orderItemPrice.orderItem.order', function ($query) use ($orderId) {
                $query->where('id', $orderId);
            })->get();
    }

    protected function getCurrentProduction($value)
    {
        return $this->productions->filter(function ($production) use ($value) {
            return $production->order_item_price_id == $value['id'] &&
                $production->size_id == $value['size_id'];
        });
    }
}
