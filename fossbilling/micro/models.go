package main

type Message struct {
	Target string	   `json:"target"`
	Action string	   `json:"action"`
	Client interface{} `json:"client"`
}

type FossbillingClient struct {
	ID		  int	 `json:"id"`
	FirstName string `json:"first_name"`
	LastName  string `json:"last_name"`
	Email	  string `json:"email"`
	Phone	  string `json:"phone"`
	Street	  string `json:"street"`
	City	  string `json:"city"`
	State	  string `json:"state"`
	PostCode  string `json:"postcode"`
	Country   string `json:"country"`
	Company   string `json:"company"`
	Currency  string `json:"currency"`
	Password  string `json:"password"`
}
