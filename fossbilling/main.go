package main

import (
	"encoding/json"
	"log"
	"time"
	"fmt"

	amqp "github.com/rabbitmq/amqp091-go"
)

func failOnError(err error, msg string) {
	if err != nil {
		log.Printf("%s: %s", msg, err)
	}
}

func main() {
	var conn *amqp.Connection
	var err error
	for conn == nil {
		conn, err = amqp.Dial("amqp://user:password@rabbitmq:5672/")
		if err != nil {
			log.Printf("Failed to connect to RabbitMQ: %s", err)
			log.Printf("Retrying in 5 seconds...")
			time.Sleep(5 * time.Second)
		}
	}
	defer conn.Close()

	ch, err := conn.Channel()
	failOnError(err, "Failed to open a channel")
	defer ch.Close()

	q, err := ch.QueueDeclare(
		"customer_sync", // name
		true,			 // durable
		false,			 // delete when unused
		false,			 // exclusive
		false,			 // no-wait
		nil,			 // arguments
	)
	failOnError(err, "Failed to declare a queue")

	msgs, err := ch.Consume(
		q.Name, // queue
		"",		// consumer
		false,	// auto-ack
		false,	// exclusive
		false,	// no-local
		false,	// no-wait
		nil,	// args
	)
	failOnError(err, "Failed to register a consumer")

	var forever chan struct{}

	go func() {
		for d := range msgs {
			log.Printf("Received a message: %s", d.Body)
			var message Message
			err := json.Unmarshal(d.Body, &message)
			if err != nil {
				log.Printf("Failed to marshal message: %s", err)
			}
			if err := handleMessage(message); err != nil {
				log.Printf("Failed to handle message: %s", err)
				// d.Nack(false, true)
				d.Ack(false)
				continue
			}
			handleMessage(message)
			d.Ack(false)
		}
	}()

	log.Printf(" [*] Waiting for messages. To exit press CTRL+C")
	<-forever
}

func handleMessage(message Message) error {
	// Switch on the message type
	switch message.Action {
	case "create":
		var client FossbillingClient
		clientBytes, err := json.Marshal(message.Client)
		if err != nil {
			return err
		}
		err = json.Unmarshal(clientBytes, &client)
		fmt.Println(client)
		return createClient(client)
	case "update":
		client := message.Client.(FossbillingClient)
		return updateClient(client)
	case "delete":
		email := message.Client.(string)
		return deleteClient(email)
	default:
		return nil
	}
}

func createClient(client FossbillingClient) error {
	fossbilling := Fossbilling{}
	fossbilling.Setup()
	reply, err := fossbilling.Call("admin", "client/create", client)
	if err != nil {
		return err
	}
	log.Printf("Reply: %s", reply)
	return nil
}

func updateClient(client FossbillingClient) error {
	return nil
}

func deleteClient(email string) error {
	return nil
}
