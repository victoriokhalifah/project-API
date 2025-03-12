<?php
namespace App\Http\Controllers;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @OA\Info(
 *     version="1.0",
 *     title="Tugas API Documentation",
 *     description="Documentation for Tugas API",
 *     @OA\Contact(
 *         email="admin@example.com"
 *     ),
 *     @OA\License(
 *         name="Apache 2.0",
 *         url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *     )
 * )
 * @OA\Server(
 *     url="http://localhost:8000/api",
 *     description="Local API Server"
 * )
 * @OA\Tag(
 *     name="Users",
 *     description="Operations about users"
 * )
 */
class UserController extends Controller
{
    public function register(UserRegisterRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $data['token'] = Str::random(60);
        
        $user = User::create($data);
        return (new UserResource($user))->response()->setStatusCode(201);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->only('username', 'password');
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $user->token = Str::random(60);
            $user->save();
            return new UserResource($user);
        }
        return response()->json(['errors' => ['message' => ['invalid credentials']]], 401);
    }

    public function get(Request $request): UserResource
    {
        return new UserResource($request->user());
    }

    public function update(UserUpdateRequest $request): UserResource
    {
        $user = $request->user();
        $data = $request->validated();
        
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        
        $user->update(array_filter($data));
        return new UserResource($user);
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->token = null;
        $user->save();
        return response()->json(['message' => 'Logged out']);
    }
}