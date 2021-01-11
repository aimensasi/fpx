<form id="form" method="post" action="{{ route('fpx.payment.auth.request') }}">
	@csrf
	<input type="hidden" name="flow" value="{{ $flow }}" />
	<input type="hidden" name="type" value="{{ $type }}" />

	<input type="hidden" name="reference_id" value="{{ $referenceId }}" />
	<input type="hidden" name="datetime" value="{{ $datetime }}" />
	<input type="hidden" name="currency" value="{{ $currency }}" />
	<input type="hidden" name="product_description" value="{{ $product_description }}" />
	<input type="hidden" name="currency" value="{{ $currency }}" />
	<input type="hidden" name="amount" value="{{ $amount }}" />
	<input type="hidden" name="customer_name" value="{{ $customer_name }}" />
	<input type="hidden" name="customer_email" value="{{ $customer_email }}" />

	<select class="w-full py-2 mt-1 px-3 border border-gray-300 rounded-md shadow-sm text-sm focus:border-indigo-300 transition-all duration-300 ease-in" name="bank_id">
		<option value="null" selected>Choose a bank</option>
		@foreach ($banks as $bank)
			<option value="{{ $bank->id }}">{{ $bank->name }} - {{ $bank->status }}</option>
		@endforeach
	</select>

	<button type="submit" {{ $attributes->merge(['class' => 'border-indigo-600 bg-indigo-600 focus:ring-indigo-400 hover:bg-indigo-400 text-white transition-all duration-300 transform ease-in hover:-translate-y-0.5 focus:outline-none hover:shadow-btn-black-200 focus:ring-4 focus:ring-gray-300 focus:ring-opacity-50 hover:opacity-75 focus:ring-4 focus:ring-opacity-50 focus:shadow-btn-black-100-inset shadow-btn-black-100 px-4 py-1 text-sm rounded-md font-semibold']) }}>{{ $title ?? 'Pay' }}</button>
</form>
