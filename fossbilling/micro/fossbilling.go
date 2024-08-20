package main

import (
    "encoding/json"
    "fmt"
    "os"
    "net/http"
    "net/http/cookiejar"
    "bytes"
    "github.com/joho/godotenv"
)

type Fossbilling struct {
    BaseUrl string
    ApiKey  string
    Client  *http.Client
}

type FossbillingReply struct {
    // Can be nil
    Result interface{} `json:"result,omitempty"`
    Error  FossbillingError `json:"error,omitempty"`
}

type FossbillingError struct {
    Message string `json:"message"`
    Code    int    `json:"code"`
}

func (f *Fossbilling) Setup() {
    f.BaseUrl = "http://fossbilling:80/api/"
    godotenv.Load()
    f.ApiKey = os.Getenv("API_KEY")
    jar, _ := cookiejar.New(nil)
    f.Client = &http.Client{
        Jar: jar,
    }
}

func (f *Fossbilling) Call(role string, method string, body interface{}) (FossbillingReply, error) {
    var reply FossbillingReply
    url := f.BaseUrl + role + "/" + method
    bodyJson, err := json.Marshal(body)
    fmt.Println("body: ", string(bodyJson))
    if err != nil {
        return reply, err
    }
    req, err := http.NewRequest("POST", url, bytes.NewBuffer(bodyJson))
    req.SetBasicAuth(role, f.ApiKey)
    req.Header.Add("Content-Type", "application/json")
    if err != nil {
        return reply, err
    }
    res, err := f.Client.Do(req)
    if err != nil {
        return reply, err
    }
    defer res.Body.Close()
    if res.Body == nil {
        return reply, nil
    }
    err = json.NewDecoder(res.Body).Decode(&reply)
    if err != nil {
        return reply, err
    }
    return reply, nil
}
