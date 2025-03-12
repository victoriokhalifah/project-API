<?php
namespace App\Http\Controllers;
use App\Http\Requests\ContactRequest;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(name="Contacts")
 * @OA\Schema(
 *     schema="Contact",
 *     required={"first_name", "last_name", "email"},
 *     @OA\Property(property="first_name", type="string", example="John"),
 *     @OA\Property(property="last_name", type="string", example="Doe"),
 *     @OA\Property(property="email", type="string", example="john.doe@example.com"),
 *     @OA\Property(property="phone", type="string", example="1234567890")
 * )
 */
class ContactController extends Controller
{
    /**
     * @OA\Get(
     *     path="/contacts",
     *     tags={"Contacts"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of contacts",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Contact")
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $contacts = Contact::where('user_id', auth()->user()->id)->get();
        return ContactResource::collection($contacts)->response();
    }

    /**
     * @OA\Post(
     *     path="/contacts",
     *     tags={"Contacts"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Contact")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Contact created",
     *         @OA\JsonContent(ref="#/components/schemas/Contact")
     *     )
     * )
     */
    public function store(ContactRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = auth()->user()->id;
        $contact = Contact::create($data);
        return (new ContactResource($contact))->response()->setStatusCode(201);
    }
}