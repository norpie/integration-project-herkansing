# .
# ├── Dockerfile
# ├── compose.yml
# ├── go.mod
# ├── go.sum
# └── main.go

FROM golang:1.23

WORKDIR /app

# pre-copy/cache go.mod for pre-downloading dependencies and only redownloading them in subsequent builds if they change
COPY go.mod go.sum ./
RUN go mod download && go mod verify

COPY . .
RUN cd /app && go build .
COPY .env /app

CMD ["./micro-fossbilling"]
