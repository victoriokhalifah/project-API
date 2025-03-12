<?php 
 
namespace App\Http\Resources; 
 
use Illuminate\Http\Request; 
use Illuminate\Http\Resources\Json\JsonResource; 
 
class ContactResource extends JsonResource 
{ 
    public function toArray(Request $request): array 
    { 
        return [ 
            'id' => $this->id, 
            'name' => $this->first_name . ' ' . $this->last_name, 
            'email' => $this->email, 
            'phone' => $this->phone, 
            'addresses' => AddressResource::collection($this->addresses) 
        ]; 
    } 
} 