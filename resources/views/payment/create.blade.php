<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Payment') }}
        </h2>
    </x-slot>

    <div x-data="payment()" x-init="initOrder($watch)" class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-1">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-1 py-1 sm:px-1 bg-white border-b border-gray-200">
                    @include('payment.partials.customer-detail')
                    @include('payment.partials.order-detail')
                    @if (($order->order_amount - $order->paid_amount > 0) || request()->has('paymentId'))
                        @include('payment.partials.payment-form')
                        
                        <div class="w-full">
                            <template x-if="loading">
                                <button class="rounded p-2 bg-white hover:bg-gray-400 text-black">
                                    {{ __('Loading...') }}
                                </button>
                            </template>
                            <template x-if="!loading">
                                <button @click="save" class="rounded p-2 bg-white hover:bg-gray-400 text-black">
                                    {{ __('Save') }}
                                </button>
                            </template>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function payment() {
                return {
                    errors: [],
                    loading: false,
                    amountDue: {{ $order->order_amount - $order->paid_amount }},
                    paidAmount: {{ $order->paid_amount }},
                    form: {
                        order_id: {{ $order->id }},
                        payment_method: '',
                        amount: '',
                        date: '',
                        meta: {
                            bank_name: '',
                            account_number: '',
                            cc_name: '',
                            cc_number: '',
                            note: ''
                        }
                    },
                    checkDpAmount() {
                        @if(request()->has('paymentId'))
                            editedPayment = {{ $order->payments->first(fn ($payment) => $payment->id ==  request('paymentId'))->amount }};
                            amountDue = {{ $order->order_amount }} - (this.paidAmount - editedPayment)

                            if (this.form.amount > amountDue) {
                                this.form.amount = amountDue
                            }
                        @else
                            if (this.form.amount > this.amountDue) {
                                this.form.amount = this.amountDue
                            }
                        @endif
                    },
                    save() {
                        let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        this.errors = []
                        this.loading = true
                        url = '{{ request()->has('paymentId') ? route('transactions.payment.update') : route('transactions.payment.store') }}'

                        fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                // 'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json, text-plain, */*',
                                'X-CSRF-TOKEN': token
                            },
                            body: JSON.stringify(this.form)
                        })
                        .then(response => response.json())
                        .then((response) => {
                            if (response.errors) {
                                this.errors = response.errors
                                this.loading = false
                            }

                            if (response.status) {
                                window.location = response.redirect
                            }
                        }).catch((error) => {
                            console.log(error);
                            this.loading = false
                        });
                    },
                    initOrder($watch) {
                        if({{ request()->has('paymentId') ? 'true' : 'false'}}) {
                            var payment = @json($order->payments->first(fn($payment) => $payment->id ==  request('paymentId')));
                            this.form = {
                                payment_id: {{ request('paymentId', 'null') }},
                                order_id: payment.order_id,
                                payment_method: payment.payment_method.replace(' ', '_'),
                                amount:  payment.amount,
                                date:  payment.payment_date,
                                meta: {
                                    bank_name: payment.meta.bank_name,
                                    account_number: payment.meta.account_number,
                                    cc_name: payment.meta.cc_name,
                                    cc_number: payment.meta.cc_number,
                                    note: payment.meta.note
                                }
                            }
                        }
                    }
                }
            }
        </script>
    @endpush
</x-app-layout>