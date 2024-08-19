package main

type FossbillingClient struct {
    ID        int    `xml:"id"`
    FirstName string `xml:"first_name"`
    LastName  string `xml:"last_name"`
    Email     string `xml:"email"`
    Phone     string `xml:"phone"`
    Street    string `xml:"street"`
    City      string `xml:"city"`
    State     string `xml:"state"`
    Zip       string `xml:"zip"`
    Country   string `xml:"country"`
    Company   string `xml:"company"`
    Currency  string `xml:"currency"`
    Password  string `xml:"password"`
}
