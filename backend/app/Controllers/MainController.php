<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\MainModel;
class MainController extends ResourceController
{
    protected $main;

    public function __construct()
    {
        $this->main = new MainModel();
    }

    public function index()
    {
        //
    }
    public function getData(){
        $data = $this->main->findAll();
        return $this->respond($data, 200);
    }
    public function signup() {
        $json = $this->request->getJSON();
    
        // Load the validation library
        $validation = \Config\Services::validation();
    
        // Define the validation rules
        $validation->setRules([
            'username' => 'required',
            'email' => 'required|valid_email|is_unique[users.email]', // Check if email is unique
            // Add more validation rules for other fields
        ]);
    
        // Validate the input data
        if (!$validation->run($json)) {
            // Validation failed, return validation errors
            return $this->respond(['success' => false, 'errors' => $validation->getErrors()], 400); // HTTP 400 Bad Request
        }
    
        // Check if the user with the same email already exists
        $existingUser = $this->main->getUserByEmail($json->email); // Replace with your method to retrieve a user by email
    
        if ($existingUser) {
            // User with the same email already exists, return a response indicating user existence
            return $this->respond(['success' => false, 'message' => 'User with this email already exists'], 200);
        }
    
        // If no existing user with the same email, proceed with user registration
        $data = [
            'username' => $json->username,
            'last_name' => $json->lastname,
            'first_name' => $json->firstname,
            'gender' => $json->selectedGender,
            'birthdate' => $json->bdate,
            'phone_number' => $json->phone,
            'email' => $json->email,
            'password' => password_hash($json->password, PASSWORD_DEFAULT),
        ];
    
        $register = $this->main->save($data);
        return $this->respond(['success' => true, 'message' => 'User registered successfully'], 200);
    }
    

}