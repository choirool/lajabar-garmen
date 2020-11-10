<div class="mt-2 flex">
    <template x-if="!form.dp.has_dp">
        <button class="rounded p-2 bg-white hover:bg-gray-400 text-black" @click="addDp">
            {{ __('Add DP') }}
        </button>
    </template>
    <template x-if="form.dp.has_dp">
        <button class="rounded p-2 bg-white hover:bg-gray-400 text-black" @click="removeDp">
            {{ __('Remove DP') }}
        </button>
    </template>
</div>
<template x-if="form.dp.has_dp">
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
                    <input 
                        type="number" 
                        min="0" 
                        x-model="form.dp.amount"
                        @change="checkDpAmount" 
                        class="w-full border border-solid border-gray-500">
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
            <template x-if="form.dp.payment_method == 'bank_transfer'">
                <tr>
                    <td>Bank name</td>
                    <td>:</td>
                    <td>
                        <input type="text" x-model="form.dp.meta.bank_name" class="w-full border border-solid border-gray-500">
                        <template x-if="errors['dp.meta.bank_name']">
                            <p class="mt-2 text-sm text-red-600"
                                x-text="errors['dp.meta.bank_name'][0]"></p>
                        </template>
                    </td>
                </tr>
            </template>
            <template x-if="form.dp.payment_method == 'bank_transfer'">
                <tr>
                    <td>Bank account number</td>
                    <td>:</td>
                    <td>
                        <input type="text" x-model="form.dp.meta.account_number" class="w-full border border-solid border-gray-500">
                        <template x-if="errors['dp.meta.account_number']">
                            <p class="mt-2 text-sm text-red-600"
                                x-text="errors['dp.meta.account_number'][0]"></p>
                        </template>
                    </td>
                </tr>
            </template>

            <tr>
                <td>Note</td>
                <td>:</td>
                <td>
                    <input type="text" x-model="form.dp.meta.note" class="w-full border border-solid border-gray-500">
                    <template x-if="errors['dp.meta.note']">
                        <p class="mt-2 text-sm text-red-600"
                            x-text="errors['dp.meta.note'][0]"></p>
                    </template>
                </td>
            </tr>
        </table>
    </div>
</template>