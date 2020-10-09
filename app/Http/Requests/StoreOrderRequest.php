<?php

namespace App\Http\Requests;

use App\Models\Item;
use App\Models\Size;
use App\Models\Color;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Material;
use App\Models\Salesman;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public $customers;
    public $salesmen;
    public $materials;
    public $categories;
    public $colors;
    public $sizes;
    public $items;

    public function __construct()
    {
        $this->customers = Customer::select('name', 'id')->orderBy('name')->get();
        $this->salesmen = Salesman::select('name', 'id')->orderBy('name')->get();
        $this->materials = Material::orderBy('name')->get();
        $this->categories = Category::orderBy('name')->get();
        $this->colors = Color::orderBy('name')->get();
        $this->sizes = Size::all();
        $this->items = Item::select('name', 'id', 'unit')->orderBy('name')->get();
    }
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'customer_id' => 'required|in:' . $this->customers->implode('id', ','),
            'salesman_id' => 'required|in:' . $this->salesmen->implode('id', ','),
            'date' => 'required|date|date_format:Y-m-d|before_or_equal:today',
            'order_lines' => 'required|array',
            'order_lines.*.item' => 'required|distinct|in:' . $this->items->implode('id', ','),
            'order_lines.*.type' => 'required|in:' . $this->categories->implode('id', ','),
            'order_lines.*.material' => 'required|in:' . $this->materials->implode('id', ','),
            'order_lines.*.color' => 'required|in:' . $this->colors->implode('id', ','),
            'order_lines.*.printing' => 'required|boolean',
            'order_lines.*.note' => '',
            'order_lines.*.price' => 'required|array',
            'order_lines.*.price.*.size_id' => 'required|in:' . $this->sizes->implode('id', ','),
            'order_lines.*.price.*.qty' => 'required|numeric|min:0',
            'order_lines.*.price.*.price' => 'required|numeric|min:0',
        ];
    }
}
