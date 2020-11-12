<div class="w-full">
    <table>
        <tr>
            <td>Payment method</td>
            <td>:</td>
            <td>
                <select x-model="form.payment_method" class="w-full border border-solid border-gray-500 bg-white">
                    <option value="">Select payment method</option>
                    <option value="cash">Cash</option>
                    <option value="cc">CC</option>
                    <option value="bank_transfer">Bank transfer</option>
                </select>
                <template x-if="errors['payment_method']">
                    <p class="mt-2 text-sm text-red-600"
                        x-text="errors['payment_method'][0]"></p>
                </template>
            </td>
        </tr>
        <tr>
            <td>Amount</td>
            <td>:</td>
            <td>
                <input 
                    type="number" 
                    min="0"
                    max="{{ $order->order_amount - $order->paid_amount  }}"
                    x-model="form.amount"
                    @change="checkDpAmount" 
                    class="w-full border border-solid border-gray-500">
                <template x-if="errors['amount']">
                    <p class="mt-2 text-sm text-red-600"
                        x-text="errors['amount'][0]"></p>
                </template>
            </td>
        </tr>
        <tr>
            <td>Date</td>
            <td>:</td>
            <td>
                <input type="date" x-model="form.date" class="w-full border border-solid border-gray-500">
                <template x-if="errors['date']">
                    <p class="mt-2 text-sm text-red-600"
                        x-text="errors['date'][0]"></p>
                </template>
            </td>
        </tr>
        <template x-if="form.payment_method == 'bank_transfer'">
            <tr>
                <td>Bank name</td>
                <td>:</td>
                <td>
                    <input type="text" x-model="form.meta.bank_name" class="w-full border border-solid border-gray-500">
                    <template x-if="errors['meta.bank_name']">
                        <p class="mt-2 text-sm text-red-600"
                            x-text="errors['meta.bank_name'][0]"></p>
                    </template>
                </td>
            </tr>
        </template>
        <template x-if="form.payment_method == 'bank_transfer'">
            <tr>
                <td>Bank account number</td>
                <td>:</td>
                <td>
                    <input type="text" x-model="form.meta.account_number" class="w-full border border-solid border-gray-500">
                    <template x-if="errors['meta.account_number']">
                        <p class="mt-2 text-sm text-red-600"
                            x-text="errors['meta.account_number'][0]"></p>
                    </template>
                </td>
            </tr>
        </template>

        <tr>
            <td>Note</td>
            <td>:</td>
            <td>
                <input type="text" x-model="form.meta.note" class="w-full border border-solid border-gray-500">
                <template x-if="errors['meta.note']">
                    <p class="mt-2 text-sm text-red-600"
                        x-text="errors['meta.note'][0]"></p>
                </template>
            </td>
        </tr>
    </table>
</div>
