##Tracking Email

Se registraran en la misma colección los eventos de tipo apertura, click y baja de la subscripción a futuras campañas de email.
Los datos que se registran son:
 - La instancia a la que hace referencia
 - El cliente que ha generado el evento
 - La promoción en la que se ha hecho click
 - La fecha
 Con el dato de la fecha se genera un `time_bucketing` para catalogar el evento en un dia, hora, semana, mes y año concreto
 para que el filtrado sea mas facil. Por ejemplo, para la fecha: **2015-12-02 10:07:45** se generará el siguiente `time_bucketing`:
 
```
 [
    "2015-12-02 10-hour",
    "2015-12-02-day",
    "2015-52-week"
    "2015-12-month",
    "2015-year"
 ]
```

Con estos datos es muy facil filtrar todos los eventos de una instancia y clasificarlos por fechas:

```
  db.getCollection('tracking').aggregate(
      {$match: {instancia: 3, time_bucket: "2015-year"}},
      {$unwind: "$time_bucket"},
      {$project: {time_bucket_event: {$concat: ["$time_bucket", "/", "$tipo"]}}},
      {$group: {
          _id: "$time_bucket_event",
          event_count: {"$sum": 1}
      }} )
```
 

