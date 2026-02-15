Estoy creando una sistema multi-sindicato para el manejo de prestamos y caja, asi como transparencia y gestiones sindicales.

## relativo a los sindicatos

se pretende que se pueda manejar multiples sindicatos y que cada sindicato sea independiente y administrado por un "super_admin"

## Relativo a los prestamos

El prestamista cuando realiza la solicitud de prestamo indica la forma en la que se le deberan efectuar los descuentos, los descuentos pueden ser quincenalmente o a sus prestaciones que son varias y que son montos varables y en fechas diversas ej el aguinaldo que se paga en diciembre aprox el 15 de diciembre, pero el monto es variable y depende de cada trabajador (del puesto y salario), lo ideal es que en la solicitud se suba el ultimo estado de cuenta donde aparezca la prestacion a descontar, pero en el prestamos se pueden agregar las prestaciones que sean necesarias, ya que se puede pedir un prestamo con solo la prestacion o con el pago quincenal o combinados. Debe tener en cuenta que el prestamo se divide en cuotas y que se puede pagar en un solo pago o en cuotas (Los pagos quincenales son en cuotas y las prestaciones en un solo pago).

En el proceso de prestamo consta de varias etapas, entre ellas

- solicitud con seleccion del monto, plazo y tipos de pagos asi como el plazo que debe ser hasta antes de diciembre (excepto las prestaciones ya que estas se cobraran en la fecha en que se paguen)
- recepcion de la solicitud y revision documental. los administradores/encargados verificaran los documentos y aprobaran la solicitud con el monto final, el plazo, y el interes. En caso de ser rechazada se explicara el porque o se podria colocar en un estado de en lista de espera.
- si la solicitud fue aceptada se generara un formato en el que se indique las formas de pago y a donde se realizara el deposito (n. de cuenta y/o clabe interbancaria, asi como el banco), asi como el pagare y una anuencia de descuento que el prestador debera descargar y firmar
- cuando la documentacion este firmada se debera subir para su validacion y en cuanto todo este validado se dara por terminado la solicitud, en caso que los documentos esten incompletos/incorrectos se debera indicar y agregar un mensaje del porque no esta correcto con el fin de que el prestador pueda corregir
- por ultimo el prestamo queda aprobado y validado con lo que el prestador puede ver su corrida financiera con los datos de cuanto pagara ya sea quincenal o con la prestacion con el interes (interes simple con el metodo aleman, interes compuesto para las prestaciones) se toma en cuenta los interese a partir de la fecha de validacion y se suman para el interes los dias faltantes para la quincena o para el interes de las prestaciones

### En el reporte simulador de prestamos considerar lo siguiente:

a) Aparte de los meses considerar también los días del préstamo:
Por ejemplo si se da el préstamo el 20 de diciembre, considerar los días del 20 de diciembre al 31 de diciembre y aparte los meses en que se pagara el préstamo.

Ejemplo:
Meses: 11
b) Dias adicionales del préstamo: 5
c) Considerar forma de pago:
Si la forma de pago es Nomina: Solicitar tiempo en meses y dias de pago.
Si la forma de pago es Prestaciones: Solicitar el monto a pedir prestado y se visualizará todas las prestaciones a las que se aplicara el préstamo.
Prestaciones:
d) En el reporte del simulador de prestamos deberá aparecer el nombre del prestamista.
e) En el simulador de prestamos, se deberá establecer a donde se efectuará el descuento, por ejemplo, el préstamo x se descontara con bono del dia del padre. O el préstamo no se pagara mensualmente sino con el aguinaldo (Que el sistema calcule todos esos meses para visualizar en el reporte cuanto se pagara al fin del año).

g) Tabla de corrida financiera (La corrida financiera debe ser quincenal, no mensual).

| Quincena | Costo fijo – Pago Capital | Interés Quincenal | Pago Quincenal | Saldo Final Quincena | Fecha de pago |
| -------- | ------------------------- | ----------------- | -------------- | -------------------- | ------------- |

h) Tambien que al final de la corrida financiera, que salga dos firmantes: El Secretario de Finanzas y el Prestamista (Que sea dinámico de la base de datos).
i) En fecha de pago que las fechas sean los días 15 y los días 20.
j) Que al final de la tabla de corrida financiera que salga la sumatoria de los montos.

### Notas

- por algunas razones puede a a alguien se le de un interes diferente a los listados, se debe contemplar
- en caso de que alguien quiera liquidar su prestamo antes se debe poder hacer una reestructuracion de la deuda con un recalculo en el interes
- en algunas ocaciones se presenta un fenomeno denomidado `picos` esto sucede cuando alguien no paga, por lo que se debe regenerar la deuda para ajustar el pago pendiente y sumar los intereses
- se presenta la ocacion en que alguien, con el fin de pagar mas rapido hace pagos anticipados, se debe regenerar para actualizar los interes a pagar
- los movimientos deben ser transparentes y generarse alguna especie de `comprobante`
- puede que una persona tenga varios prestamos se deberia poder imprimir o generar alguna especie de `estado de cuenta`
- algunos cambios podrian ser manuales, como por ejemplo los intereses, aunque tambien hay que considerar los siguientes:
  - Agremiado y ahorrador 6%
  - Agremiado y no ahorrador 7.5%
  - No agremiado y ahorrador (Externo) 8%
  - No agremiado y no ahorrador (Externo) 9.5

## relativo a el control de la caja

el gasto o ingreso que se haga a la caja se debera justificar con un comprobante y con la fecha del gasto, ademas de agregar detalles relevantes que apoten valor. Se deberia poder, en el tema de transparencia tener la opcion de compartir o mostrar en algun lugar los gastos o ingresos que tiene el sindicato para prevenir el lavado de dinero y prevenir la corrupcion (opcional)

los cortes deben ser quincenales y se debe poder desglosar (solo para usaurios autorizados)

## relativo a tranparencia

transparencia hara referencia a todos los documentos como informacion sobre gestiones sindicales, recepcion y en general archivos diversos para su consulta documental, los archivos estaran en pdf mayormente y se deberan organizar por anos y meses, asi como tambien habra documentos privados (solo ciertas personas/roles podran ver) se debera hacer busquedas por nombre y/o tipos.

Se prevee subir mas de 1000 documentos.

## relativo a los usuarios

cada usaurio debe pertenecer a un sindicato y puede o no ser agremiado, esto suena contradictorio, pero en ocaciones trabajadores que no pertenecen al sindicato acuden a realizar prestamos y el sindicato se los da (con un interes diferente) pero como tal no pertenecen al sindicato si no, que mas bien solo estan por el tema de prestamos.

un sindicato tiene su comite con diferentes puestos, es algo que debe ser personalizable ya que es unico por sindicato (algunos tienen secretario general, finanzas, etc). y a su vez tienen diferentes responsabilidades, por lo que un usuario puede tener disponible secciones o apartados en la plataforma segun la funcion que ocupe, por ejemplo alguien de finanzas llevaria el control de la caja, pero un agremiado no

## relativo a las publicaciones

cada sindicato podra publicar noticias, avisos, gestiones etc, con informacion relevante, imagenes, quizas enlaces o archivos adjuntos

## relativo a los mensajes

se tendra un canal de comunicacion para distintas acciones, por ejemplo puede que alguien quiera informacion o tenga una duda y quiera contactarse, o que tenga una duda respecto a la transparencia, puede que un usuario quiera prguntar algo respecto a su prestamo o a algun inconveniente a traves de su buzon. se deben cubrir esos puntos

## otras caracteristicas

- generacion de credenciales solo si el usuario es agremiado o pertenece como miembro a un sindicato.
- felicitaciones a los miembros por su cumpleanos
- agendar citas

---
