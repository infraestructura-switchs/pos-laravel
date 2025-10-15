<?php

namespace App\Services;

use App\Models\Customer;
use App\Rules\Identification;
use App\Enums\CustomerTributes;
use App\Rules\Phone;
use App\Enums\LegalOrganization;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CustomerService
{
    public function store(array $input): int
    {
        $validator = Validator::make($input, [
            'identification_document_id' => 'required|exists:identification_documents,id',
            'legal_organization' => ['required', Rule::in(LegalOrganization::getCases())],
            'tribute' => ['required', Rule::in(CustomerTributes::getCases())],
            'no_identification' => ['required', 'string', new Identification, 'unique:customers'],
            'dv' => 'required_if:identification_document_id,6|min:0|max:9',
            'names' => 'required|string|min:5|max:250',
            'direction' => 'nullable|string|max:250',
            'phone' => ['nullable', 'string', new Phone],
            'email' => 'nullable|string|email|max:250',
        ]);

        $validator->validate();

        $customer = Customer::create($input);

        return $customer->id;
    }

    public function update(int $id, array $input): bool
    {
        $customer = Customer::findOrFail($id);

        $validator = Validator::make($input, [
            'identification_document_id' => 'required|exists:identification_documents,id',
            'legal_organization' => ['required', Rule::in(LegalOrganization::getCases())],
            'tribute' => ['required', Rule::in(CustomerTributes::getCases())],
            'no_identification' => ['required', 'string', new Identification, Rule::unique('customers', 'no_identification')->ignore($customer->id)],
            'dv' => 'required_if:identification_document_id,6|min:0|max:1',
            'names' => 'required|string|min:5|max:250',
            'direction' => 'nullable|string|max:250',
            'phone' => ['nullable', 'string', new Phone],
            'email' => 'nullable|string|email|max:250',
            'top' => 'required|integer|min:0|max:1',
            'status' => 'required|integer|min:0|max:1',
        ]);

        $validator->validate();

        $customer->update($input);

        return true;
    }

    public function getById(int $id): Customer
    {
        return Customer::findOrFail($id);
    }

    public function getByFilters(array $filters, int $perPage = 15)
    {
        $query = Customer::query();

        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (!empty($filters['document_number'])) {
            $query->where('document_number', 'like', '%' . $filters['document_number'] . '%');
        }

        if (!empty($filters['email'])) {
            $query->where('email', 'like', '%' . $filters['email'] . '%');
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('id', 'desc')->paginate($perPage);
    }
}
