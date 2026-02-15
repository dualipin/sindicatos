1.

- Tiene algunas validaciones ya que el corazon del sistema es un modulo de prestamos con varias reglas que pueden llegar a cambiar, como tazas de prestamos, plazos fechas.
- si, hay aprobacion de prestamos de varios pasos, como subida de documentos
- puede que el dominio quiza no cambie

2.

- solo html (usando latte) no pretendo crear una API rest o aplicacion cli (solo comandos basicos para realizar importaciones simples)
- limitacion del hosting compartido (php 8.3 con apache y mysql bastante restringido)
- el sistema de momento solo sera para un solo sindicato pero se tiene planeado que varios sindicatos lleguen a usar el sistema

3.

- considero que la base de datos esta en la version final lista para produccion (te pasare la estructura)
- latte sera el unico a usar, no json, no API REST
- No se tiene pensado migrar a un framework

4.

- Los testing NO son un requisito
- No quiero testear la logica del negocio sin base de datos (por el trabajo individual)
- me es indiferente mockear infra, de momento prefiero no

5.

- lo que mas cambio es la interfaz la mayor parte del tiempo, seguida de reportes y algunas reglas que se reajustan segun requisitos
- me duele modificar codigo en el core, todo lo que tenga que ver con logica "pesada" cambiar cosas como consultas, la forma en que se traen los datos

6.

- no se cuantas entidades tenga en el futuro, puede que ya no le yo mantenimiento en ese entonces
- probablemente si espero que otros desarrolladores participen, uno mas que tiene conocimientos limitados
- actualmente es un producto interno, se tiene la idea de convertirlo en un SaaS por eso quiero hacerlo multi-tenant, aunque aun no estoy seguro

7.

- procuro ser ordenado y mantener una estructura limpia, pero el proyecto crece conforme piden cosas
- me interesa que sea algo simple pero sin llegar a ser algo insostenible, personalmente me gustan las cosas explicitas y quiero que sea facil para todos (sobre todo agent-friendly)

8.

- no tengo requisitos fuertes de performance, habrán al menos 80 usuarios
- creo que no hay operaciones criticas financieras, pero el sistema debe ser "flexible" con algunas cuentas, la mayoría de las operaciones serán para control de gastos y gestión de prestamos sindicales

---

1. Se consolidan, pueden tener varios siempre que se le aprueben
2. Si de debe poder, aunque eso debe quedar a consideracion de los responsables (secretario general, finanzas, etc)
3. Se cobrara según el tipo ya sea compuesto o simple
4. Ellos mismos se deberar registrar en un apartado especifico, los documentos que deben subir son los estados de cuenta correspondientes, en su registro deberan subir su ine y comprobante de domicilio
5. El administrador o administradores seran los encargados de hacer cualquier validacion
6. la firma es autografa
7. de momento solo operara un sindicato
8. debe ser flexible ya que hay diferentes contextos en algunos casos solo es para preguntar y recibir respuesta por otro medio, otras veces funcionara como un chat (NO se contempla tiempo real), otras veces como un formulario, etc
