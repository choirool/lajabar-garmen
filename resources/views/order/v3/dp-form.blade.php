<div class="w-full">
    <table>
        <tr>
            <td>Payment method</td>
            <td>:</td>
            <td>
                <select x-model="form.dp.payment_method" class="w-full border border-solid border-gray-500 bg-white">
                    <option value="">Select payment method</option>
                    <option value="cash">Cash</option>
                    <option value="cc">CC</option>
                    <option value="bank_transfer">Bank transfer</option>
                </select>
                <template x-if="errors['dp.payment_method']">
                    <p class="mt-2 text-sm text-red-600"
                        x-text="errors['dp.payment_method'][0]"></p>
                </template>
            </td>
        </tr>
        <tr>
            <td>Amount</td>
            <td>:</td>
            <td>
                <input type="number" min="0" x-model="form.dp.amount" class="w-full border border-solid border-gray-500">
                <template x-if="errors['dp.amount']">
                    <p class="mt-2 text-sm text-red-600"
                        x-text="errors['dp.amount'][0]"></p>
                </template>
            </td>
        </tr>
        <tr>
            <td>Date</td>
            <td>:</td>
            <td>
                <input type="date" x-model="form.dp.date" class="w-full border border-solid border-gray-500">
                <template x-if="errors['dp.date']">
                    <p class="mt-2 text-sm text-red-600"
                        x-text="errors['dp.date'][0]"></p>
                </template>
            </td>
        </tr>
    </table>
</div>