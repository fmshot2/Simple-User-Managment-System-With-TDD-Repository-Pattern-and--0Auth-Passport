<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Interfaces\UserRepositoryInterface;
use App\Classes\ApiResponseClass;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{

    private UserRepositoryInterface $userRepositoryInterface;

    public function __construct(UserRepositoryInterface $userRepositoryInterface)
    {
        $this->userRepositoryInterface = $userRepositoryInterface;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->userRepositoryInterface->index();

        return ApiResponseClass::sendResponse(UserResource::collection($data), '', 200);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $details = $request->validated();
        $details['password'] = Hash::make($request->password);
        DB::beginTransaction();
        try {
            $user = $this->userRepositoryInterface->store($details);
            //Geerate Passport Access Token
            $token = $user->createToken('LaravelAuthApp')->accessToken;

            DB::commit();
            return ApiResponseClass::sendUserResponse(new UserResource($user), $token, 201);
        } catch (\Exception $ex) {
            throw $ex;
            return ApiResponseClass::rollback($ex);
        }
    }


    /**
     * Display the all users resource.
     */
    public function showAll(Request $request)
    {
        try {
            return ApiResponseClass::sendResponse(UserResource::collection(User::all()), '', 200);
        } catch (\Exception $ex) {
            return ApiResponseClass::throw($ex);
        }
    }

    /**
     * Display the single user resource.
     */
    public function show($id )
    {
        try {
            if (!User::where('id', $id)->first()) {
                     return $response = [
                    'success' => false,
                    'data'    => "invalid id"
                ];
            }
            $user = $this->userRepositoryInterface->getById($id);

            return ApiResponseClass::sendResponse(new UserResource($user), '', 200);
        } catch (\Exception $ex) {
            return ApiResponseClass::throw($ex);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, $id)
    {
        $updateDetails = $request->validated();
        $updateDetails['password'] = Hash::make($request->password);

        DB::beginTransaction();
        try {
            $user = $this->userRepositoryInterface->update($updateDetails, $id);

            DB::commit();
            return ApiResponseClass::sendResponse('User Update Successful', '', 200);
        } catch (\Exception $ex) {
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * Remove the single user resource from storage.
     */
    public function destroy($email)
    {
        try {
            $this->userRepositoryInterface->delete($email);
            return ApiResponseClass::sendResponse('User delete Successful', '', 200);
        } catch (\Exception $ex) {
            return ApiResponseClass::rollback($ex);
        }
    }
}
