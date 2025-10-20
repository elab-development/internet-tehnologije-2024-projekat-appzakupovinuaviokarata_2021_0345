<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Booking #{{ $b->id }}</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
    .box { border:1px solid #333; padding:16px; }
    .row { display:flex; justify-content:space-between; }
    h1 { font-size:18px; margin:0 0 8px 0; }
  </style>
</head>
<body>
  <div class="box">
    <h1>Flight Ticket / Booking #{{ $b->id }}</h1>

    <div class="row">
      <div>
        <strong>Passenger:</strong> {{ $b->user->name }}<br>
        <strong>Email:</strong> {{ $b->user->email }}
      </div>
      <div>
        <strong>Status:</strong> {{ $b->status }}<br>
        <strong>Issued:</strong> {{ $b->created_at->format('Y-m-d H:i') }}
      </div>
    </div>

    <hr>

    <p>
      <strong>Flight:</strong> {{ $b->flight->flight_no }}
      ({{ $b->flight->carrier->code }})<br>
      <strong>Route:</strong> {{ $b->flight->origin->iata }} → {{ $b->flight->destination->iata }}<br>
      <strong>Departure:</strong> {{ $b->flight->dep_time->format('Y-m-d H:i') }}<br>
      <strong>Arrival:</strong> {{ $b->flight->arr_time->format('Y-m-d H:i') }}
    </p>

    <p>
      <strong>Fare:</strong> {{ $b->fare->cabin_class }}
      — {{ number_format($b->fare->price,2) }} {{ $b->fare->currency }}
    </p>

    <hr>
    <p>Thank you for choosing Flight Tickets.</p>
  </div>
</body>
</html>
