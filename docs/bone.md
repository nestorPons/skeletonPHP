## Sentencias

Bone dispone de varias sentencias con las que dinamizar la estructura de la aplicación.

### Sentencia id
```
@if(..condición)
    Contenido ...
@else
    Contenido ...
@endif 
``` 

### Sentencia for
```
@for({"a":1,"b":2,"c":3})
    Contenido ...
    @key 
    @value 
@endfor 
```

### Sentencia include
```
@include(url)
```