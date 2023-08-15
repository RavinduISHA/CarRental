<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.7.1/gsap.min.js"></script>
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')

</head>

@php
    $user = Auth::user();
@endphp

<body>
    <!-- start navigation -->
    @include('layouts.navigation')
    <!-- end navigation -->
    <!-- section one -->
    <div class="flex md:flex-wrap md:justify-center md:items-start">
        <div class=" p-4 md:w-full lg:w-4/12 grid grid-flow-row mt-24 sm:mt-18">

            <div class="bg-[#F8FFF2] p-8">

                <!-- car image -->
                <div class="w-full">
                    <img class="w-full" src="{{ Storage::url($bookingData->vehicle->images[0]->file_path) }}">
                </div>

                <!-- pickup details -->
                <table class="w-full table-fixed mt-5">
                    <tbody>
                        <tr>
                            <td class="border-b-2 border-dotted p-2">Title</td>
                            <td class="border-b-2 border-dotted text-right p-2">{{ $bookingData->vehicle['year']}} {{
                                $bookingData->vehicle['make']}} {{ $bookingData->vehicle['model']}} {{
                                $bookingData->vehicle['body_type']}}</td>
                        </tr>
                        <tr>
                            <td class="border-b-2 border-dotted p-2">Pick-up Date & Time</td>
                            <td class="border-b-2 border-dotted text-right p-2"> {{ $bookingData['pickup_time']}}</td>
                        </tr>
                        <tr>
                            <td class="border-b-2 border-dotted p-2">Drop-off Date & Time</td>
                            <td class="border-b-2 border-dotted text-right p-2">{{ $bookingData['dropoff_time']}}</td>
                        </tr>
                        <tr>
                            <td class="border-b-2 border-dotted p-2">Total Days</td>
                            <td class="border-b-2 border-dotted text-right p-2">{{ $bookingData['bookingDaysCount']}}
                            </td>
                        </tr>
                        <tr>
                            <td class="border-b-2 border-dotted p-2">Pick-up Location</td>
                            <td class="border-b-2 border-dotted text-right p-2">{{ $bookingData['pickup']}}</td>
                        </tr>
                        <tr>
                            <td class="p-2">Drop-off Location</td>
                            <td class="text-right p-2">{{ $bookingData['dropoff']}}</td>
                        </tr>
                    </tbody>
                </table>

            </div>

            <div class="border-2 border-[#398564] p-8 rounded-xl mt-6">

                <h1 class="text-center text-[#398564] font-semibold text-lg">Order Summary</h1>
                <!-- order summery -->
                <table class="w-full table-fixed mt-5">
                    <tbody>
                        <tr>
                            <td class="border-b-2 border-dotted p-2">SUBTOTAL</td>
                            <td class="border-b-2 border-dotted text-right p-2">AUD {{ $bookingData['bookingDaysCount'] * $bookingData->vehicle['price']}}.00</td>
                        </tr>
                        <tr>
                            <td class="border-b-2 border-dotted p-2">FINES</td>
                            <td class="border-b-2 border-dotted text-right p-2">AUD 0.00</td>
                        </tr>
                        <tr>
                            <td class="border-b-2 border-dotted p-2">INSURANCE</td>
                            <td class="border-b-2 border-dotted text-right p-2">AUD 0.00</td>
                        </tr>
                        <tr>
                            <td class="p-2 mt-2 font-bold">TOTAL</td>
                            <td class="text-right p-2 font-bold">AUD {{ $bookingData['bookingDaysCount'] * $bookingData->vehicle['price']}}.00</td>
                        </tr>
                    </tbody>
                </table>

            </div>
        </div>
        <div class="md:w-full lg:w-4/12 grid grid-flow-row h-screen mt-24 sm:mt-18">
            <form action="{{route('payment.saving')}}" method="post" id="payment-form">
                @csrf
                <div class="bg-[#F8FFF2] grid md:p-8 md:mt-0">
                    @if($errors->any())
                        <div class="alert alert-danger" role="alert">
                            {!! implode('', $errors->all('<div class="text-red-500">:message</div>')) !!}
                        </div>
                    @endif

                    <h1 class="text-[#707070] text-base">Let's get your personal account set up.</h1>
                    <hr>

                    <div class>
                        <div class="flex items-center space-x-1">
                            <h1 class="text-black text-sm">Country <span class="text-red-500">*</span></h1>
                            <select class="w-3/6 rounded-md border-none shadow-md" name="country" id="country">
                                @foreach ($countries as $country)
                                <option value="{{$country->name}}" @if((old('country') ?? $user->country) == $country->name) selected @endif>{{$country->name}} - {{$country->code}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <h1 class="text-[#707070] text-base mt-6">Your Personal Information</h1>
                    <hr>

                    <div class="flex w-full gap-4 mt-5">
                        <div class="flex-1">
                            <h1 class="text-black text-sm">First Name <span class="text-red-500">*</span></h1>
                            <input class="w-full rounded-md border-none shadow-md" type="text" name="first_name" id="" value="{{old('first_name') ?? $user->first_name ?? ''}}">
                        </div>
                        <div class="flex-1">
                            <h1 class="text-black text-sm">Last Name <span class="text-red-500">*</span></h1>
                            <input class="w-full rounded-md border-none shadow-md" type="text" name="last_name" id="" value="{{old('last_name') ?? $user->last_name ?? ''}}">
                        </div>
                    </div>

                    <div class="flex w-full gap-4">
                        <div class="flex-1">
                            <h1 class="text-black text-sm">Mobile Number <span class="text-red-500">*</span></h1>
                            <input class="w-full rounded-md border-none shadow-md" type="text" name="mobile" id="" value="{{old('mobile') ?? $user->mobile ?? ''}}">
                        </div>
                        <div class="flex-1">
                            <h1 class="text-black text-sm">Email <span class="text-red-500">*</span></h1>
                            <input class="w-full rounded-md border-none shadow-md" type="text" name="email" id="" value="{{old('email') ?? $user->email ?? ''}}">
                        </div>
                    </div>

                    <div class="flex w-full gap-4">
                        <div class="flex-1">
                            <h1 class="text-black text-sm">Address 1 <span class="text-red-500">*</span></h1>
                            <input class="w-full rounded-md border-none shadow-md" type="text" name="Address_1" id="" value="{{old('Address_1') ?? $user->Address_1 ?? ''}}">
                        </div>
                        <div class="flex-1">
                            <h1 class="text-black text-sm">Address 2 <span class="text-red-500">*</span></h1>
                            <input class="w-full rounded-md border-none shadow-md" type="text" name="Address_2" id="" value="{{old('Address_2') ?? $user->Address_2 ?? ''}}">
                        </div>
                    </div>

                    <div class="flex w-full gap-4">
                        <div class="flex-1">
                            <h1 class="text-black text-sm">City <span class="text-red-500">*</span></h1>
                            <input class="w-full rounded-md border-none shadow-md" type="text" name="city" id="" value="{{old('city') ?? $user->city ?? ''}}">
                        </div>
                        <div class="flex-1">
                            <h1 class="text-black text-sm">Zip/Postal Code <span class="text-red-500">*</span></h1>
                            <input class="w-full rounded-md border-none shadow-md" type="text" name="zip" id="" value="{{old('zip') ?? $user->zip ?? ''}}">
                        </div>
                    </div>

                    <h1 class="text-[#707070] text-base mt-8">Driver's License Information</h1>

                    <p class="text-[#707070] text-sm">
                        We request this information so on your first rental you can skip
                        the airport counter and pick your car up directly at the lot.
                    </p>
                    <hr class="">
                    <div class="flex w-full gap-4 mt-5">
                        <div class="flex-1">
                            <h1 class="text-black text-sm">Driver's License Number <span class="text-red-500">*</span>
                            </h1>
                            <input class="w-full rounded-md border-none shadow-md" type="text" name="driving_license" value="{{old('driving_license') ?? $user->driving_license ?? ''}}"
                                id="">
                        </div>
                        <div class="flex-1">
                            <h1 class="text-black text-sm">Expired Year (YYYY) <span class="text-red-500">*</span></h1>
                            <input class="w-full rounded-md border-none shadow-md" type="number" value="{{old('driving_license_expire_year') ?? $user->driving_license_expire_year ?? ''}}"
                                name="driving_license_expire_year" id="">
                        </div>
                    </div>
                    <div class="flex w-full gap-4">
                        <div class="flex-1">
                            <h1 class="text-black text-sm">Expired Month <span class="text-red-500">*</span></h1>
                            <input class="w-full rounded-md border-none shadow-md" type="number" min="1" max="12" value="{{old('driving_license_expire_month') ?? $user->driving_license_expire_month ?? ''}}"
                                name="driving_license_expire_month" id="">
                        </div>
                        <div class="flex-1">
                            <h1 class="text-black text-sm">Expired Date (DD) <span class="text-red-500">*</span></h1>
                            <input class="w-full rounded-md border-none shadow-md" type="number" min="1" max="31" value="{{old('driving_license_expire_date') ?? $user->driving_license_expire_date ?? ''}}"
                                name="driving_license_expire_date" id="">
                        </div>
                    </div>
                    <h1 class="text-[#707070] text-base mt-8">Credit Card Information</h1>
                    <p class="text-[#707070] text-sm">A credit card is required to keep on file for future reservations,
                        no charge for enrolling. Debit Cards can NOT be used online for enrollment or for card updates
                        for Automobex car Rental.</p>
                    <hr class="">

                    {{-- << CREDIT CARD INFO --}} 

                    <div class="w-full">
                        <form action="" id="payment-form">
                            <div id="card_details" class="mt-5 flex flex-col gap-5 relative">
                                <input type="text" name="card_holder_name" id="card-holder-name"
                                    class="w-full rounded-md border-none bg-white shadow-md p-3" placeholder="Name on card">

                                <div class="w-full rounded-md border-none bg-white shadow-md px-3">
                                    <div id="card-number" class="form-control my-auto py-3"></div>
                                </div>

                                <div class="w-full rounded-md border-none bg-white shadow-md px-3">
                                    <div id="card-cvc" class="form-control py-3"></div>
                                </div>

                                <div class="w-full rounded-md border-none bg-white shadow-md px-3">
                                    <div id="card-expiry" class="form-control py-3"></div>
                                </div>

                                <div id="card-errors" class="form-control py-3 absolute bottom-0"></div>

                                <button 
                                    type="button"
                                    id="card-button"
                                    class="rounded-md border-none bg-green-900 text-white shadow-md p-3 w-1/2 ms-auto"
                                >
                                Process Payment
                                </button>
                            </div>
                        </form>

                        <script src="https://js.stripe.com/v3/"></script>

                        <script>
                            cardDetails = document.getElementById('card_details');

                            const stripe = Stripe('{{ env('STRIPE_KEY') }}');
                            const errorElement = document.getElementById('card-errors');
                            const form = document.getElementById("payment-form")
                            const elements = stripe.elements();

                            var elementStyles = {
                                base: {
                                    color: '#212121',
                                    fontSize: '16px',
                                    ':focus': {
                                        color: '#212121',
                                    },

                                    '::placeholder': {
                                        color: '#757575',
                                    },

                                    ':focus::placeholder': {
                                        color: '#616161',
                                    },
                                },
                                invalid: {
                                    color: '#b71c1c',
                                    ':focus': {
                                        color: '#b71c1c',
                                    },
                                    '::placeholder': {
                                        color: '#e57373',
                                    },
                                },
                            };

                            const cardNumberElement = elements.create('cardNumber', {
                                style: elementStyles,
                                showIcon: true,

                            });
                            const cardExpiryElement = elements.create('cardExpiry', {
                                style: elementStyles
                            });
                            const cardCvcElement = elements.create('cardCvc', {
                                style: elementStyles
                            });

                            cardNumberElement.mount('#card-number');
                            cardExpiryElement.mount('#card-expiry');
                            cardCvcElement.mount('#card-cvc');

                            const cardHolderName = document.getElementById('card-holder-name');
                            const cardButton = document.getElementById('card-button');

                            cardButton.addEventListener('click', async (e) => {
                                const {
                                    paymentMethod,
                                    error
                                } = await stripe.createPaymentMethod(
                                    'card', cardNumberElement, {
                                    billing_details: {
                                        name: cardHolderName.value
                                    }
                                }
                                );

                                if (error) {
                                    errorElement.style.display = 'block';
                                    errorElement.textContent = error.message;
                                } else {
                                    errorElement.style.display = 'none';
                                    errorElement.textContent = "";

                                    var hiddenInput = document.createElement('input');
                                    hiddenInput.setAttribute('type', 'hidden');
                                    hiddenInput.setAttribute('name', 'payment_method');
                                    hiddenInput.setAttribute('value', paymentMethod.id);
                                    form.appendChild(hiddenInput);
                                    form.submit();
                                }
                            });
                        </script>
                </div>
                {{-- CREDIT CARD INFO >> --}}

        </div>
        </form>
    </div>
    </div>
    <!-- end section one -->

    <!-- start navigation -->
    <div class="">
        @include('layouts.footer')
    </div>
    <!-- end navigation -->

</body>

</html>