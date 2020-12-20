<div class="w-full flex bg-blue-200 px-3 py-3 mb-5">
    <div class="w-1/3 my-2">
        <table>
            <tr>
                <td>Customer name</td>
                <td>:</td>
                <td>
                    <select x-model="form.customer_id" class="w-full bg-white" @change="customerSelected($event.target.value)">
                        <option value="">Select customer</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>
                    <template x-if="errors.customer_id">
                        <p class="mt-2 text-sm text-red-600"
                            x-text="errors.customer_id[0]"></p>
                    </template>
                </td>
            </tr>
            <tr>
                <td>Date</td>
                <td>:</td>
                <td>
                    <input type="date" x-model="form.date" class="w-full">
                    <template x-if="errors.date">
                        <p class="mt-2 text-sm text-red-600"
                            x-text="errors.date[0]"></p>
                    </template>
                </td>
            </tr>
            <tr>
                <td>Sales</td>
                <td>:</td>
                <td>
                    <select x-model="form.salesman_id" class="w-full bg-white">
                        <option value="">Select salesman</option>
                        @foreach ($salesmen as $salesman)
                            <option value="{{ $salesman->id }}">{{ $salesman->name }}</option>
                        @endforeach
                    </select>
                    <template x-if="errors.salesman_id">
                        <p class="mt-2 text-sm text-red-600"
                            x-text="errors.salesman_id[0]"></p>
                    </template>
                </td>
            </tr>
        </table>
    </div>
    <div class="w-1/2 my-2">
        <table>
            <tr>
                <td>Phone</td>
                <td>:</td>
                <td x-text="selectedCustomer.phone"></td>
            </tr>
            <tr>
                <td>Country</td>
                <td>:</td>
                <td x-text="selectedCustomer.country"></td>
            </tr>
            <tr>
                <td>Inovice name</td>
                <td>:</td>
                <td>
                    <textarea x-model="form.invoice_name"></textarea>
                </td>
            </tr>
        </table>
    </div>
</div>