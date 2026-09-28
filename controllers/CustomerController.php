<?php

// Bring in the Customer model class
require_once __DIR__ . "/../classes/CustomerClass.php";

// The controller sits between the "outside world" (actions/functions/views)
// and the model (Customer). Its job is to receive plain data, pass it to
// the model, and hand back whatever the model returns. This keeps the
// model focused on the database, and keeps things like forms/JSON out of
// the model entirely.
class CustomerController
{
    // Holds the Customer instance this controller talks to.
    private $customer;

    // Runs automatically when `new CustomerController()` is called.
    // Creates one Customer instance (and therefore one database
    // connection, since Customer extends Database) for this controller
    // to reuse across its methods.
    public function __construct()
    {
        $this->customer = new CustomerClass();
    }

    // Insert a new customer.
    public function register($name, $email, $pass, $country, $city, $contact, $image = null)
    {
        if ($this->customer->emailExists($email)) {
            return ['success' => false, 'error' => 'Email already registered'];
        }
        
        $result = $this->customer->addCustomer($name, $email, $pass, $country, $city, $contact, $image);
        if ($result) {
            return ['success' => true];
        }
        return ['success' => false, 'error' => 'Registration failed'];
    }

    // Get the full list of customers from the model.
    public function selectAll()
    {
        return $this->customer->getAllCustomers();
    }

    public function login($email, $pass)
    {
        $result = $this->customer->login($email, $pass);
        if ($result) {
            return ['success' => true, 'user' => $result];
        }
        return ['success' => false, 'error' => 'Invalid email or password'];
    }
}
