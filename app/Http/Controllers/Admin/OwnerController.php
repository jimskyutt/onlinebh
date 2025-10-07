<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OwnerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $owners = User::withCount(['boardingHouses' => function($query) {
                $query->where('status', '!=', 'deleted');
            }])
            ->with(['boardingHouses' => function($query) {
                $query->select('id', 'user_id', 'name', 'contact_person', 'contact_number', 'status')
                      ->where('status', '!=', 'deleted');
            }])
            ->where('role', 'owner')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.owners.index', compact('owners'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /**
     * Get the user instance for the bound value.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    protected function findUser($value, $field = null)
    {
        $user = User::where('user_id', $value)->first();
        
        if (!$user) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("No query results for user with ID {$value}");
        }
        
        return $user;
    }
    
    public function update(Request $request, $id)
    {
        try {
            // Debug log all request data and parameters
            \Log::info('Update Request Data:', [
                'all_input' => $request->all(),
                'route_parameter_id' => $id,
                'user_id_from_request' => $request->input('user_id'),
                'method' => $request->method(),
                'is_ajax' => $request->ajax(),
                'headers' => $request->headers->all()
            ]);
            
            // Use the findUser method to get the user by user_id
            $user = $this->findUser($id);
            
            $validationRules = [
                'username' => [
                    'sometimes',
                    'required',
                    'string',
                    'max:255',
                    \Illuminate\Validation\Rule::unique('users', 'username')->ignore($id, 'user_id')
                ]
            ];
            
            // Only validate password if it's being updated
            if ($request->has('password') && !empty($request->password)) {
                $validationRules['password'] = 'required|string|min:8';
            }
            
            $validated = $request->validate($validationRules);
            
            $response = [
                'success' => true,
                'message' => 'No changes detected',
                'data' => [
                    'username' => $user->username,
                ]
            ];
            
            $updated = false;
            
            if (isset($validated['username']) && $validated['username'] !== $user->username) {
                $user->username = $validated['username'];
                $updated = true;
                $response['message'] = 'Username updated successfully';
            }
            
            if (isset($validated['password']) && !empty($validated['password'])) {
                // Only update password if it's different from the current one
                if (!\Illuminate\Support\Facades\Hash::check($validated['password'], $user->password)) {
                    $user->password = bcrypt($validated['password']);
                    $updated = true;
                    $response['message'] = 'Password updated successfully';
                } else {
                    $response['message'] = 'New password must be different from the current one';
                    $response['success'] = false;
                    return response()->json($response, 422);
                }
            }
            
            if ($updated) {
                $user->save();
                $response['data']['username'] = $user->username;
            }
            
            return response()->json($response);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
