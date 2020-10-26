<?php

namespace App\Http\Requests;

use App\Models\Category;
use App\Models\Color;
use App\Models\Item;
use App\Models\Material;
use Illuminate\Foundation\Http\FormRequest;

class ManageProductRequest extends FormRequest
{
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
        $items = Item::select('id')->get()->implode('id', ',');
        $categories = Category::select('id')->get()->implode('id', ',');
        $materials = Material::select('id')->get()->implode('id', ',');
        $colors = Color::select('id')->get()->implode('id', ',');

        return [
            'customer_id' => 'required|exists:customers,id',
            'items' => 'required|array',
            'items.*.item_id' => 'required|in:' . $items,
            'items.*.item_combination' => 'required|distinct',
            'items.*.price' => 'required|numeric',
            'items.*.type' => 'required|in:' . $categories,
            'items.*.material_id' => 'required|in:' . $materials,
            'items.*.color_id' => 'required|in:' . $colors,
            'items.*.note' => 'sometimes|max:225',
            'items.*.screen_printing' => 'required|boolean',
        ];
    }
}
