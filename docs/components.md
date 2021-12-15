# Componentes Skeleton

## Descripción
Los componentes son trozos de codigo independientes que generan un objeto html con características singulares.
Son objetos encapsulados con una estructura HTML , un diseño CSS, y un comportamiento con JS. 
Ayudan a crear aplicaciones más fáciles de diseñar y mantener.
Son fácilmente reutilizables, minimizan y homogenizan todo muestro código. 

Para un mayor dinamismo cuenta con distintas tecnologías para darle mayor funcionalidad:  

![ESQUEMA](img/Sistemacomponentes.png "Esquema del SkeletonPHP")

## Estructura 
La forma de crearlo es en un archivo con el nombre del componente que se desea.
Se debe iniciar y acabar con el pseudo-tag <code>&lt;component id=""&gt;Codigo...&lt;/component&gt;</code>. 
Dentro de el incluiremos la estructura html, js y css si se desea, y será precompilada con las directivas Bone. 

Podemos encapsular sus propios estilos <code>&lt;style lang="" scoped&gt;&lt;style&gt;</code> con el atributo lang se declarará el sublenguaje que se esté empleando: Lesscss, Sass etc..
Para añadir la funcionalidad al componente se declara la etiqueta &lt;script scope&gt;&lt;script&gt;

Los atributos que se le pasan al componente a su llamada se declaran como variables Bone.

### Atributos especiales

Existen algunos atributos especiales para el manejo y procesado de los componentes. 

* **compile = [true|false]**  
Este atributo indica si el contenido debe ser procesado con el preprocesado Bone. 
Si se omite el atributo se considera true por defecto y sera procesado el contenido. 

### Directivas
Existen distintas directivas para el manejo de los componentes:  

* **--id**  
Esta directiva genera un identificador único para nuestro componente. 
```
<component id="--id"></component>
```
* **--content**  
Obtiene el contenido en la declaración del componente y reemplaza la palabra clave por el contenido.
index.phtml
```
<m-div>Mi contenido</m-div>
```
m-div.phtml
```
<component>
<div>Este elemento tiene: --contenido</div>
@if ($$spinner) <i class="spinner"></i> @endif
</component>
```

Salida
``` 
<div>Este elemento tiene: Mi contenido</div>
```
## Estilos 

Los estilos son declarados dentro del componente mediante el tag:
```
<style lang="" scope>
    #--id {
        css|less|sassc
    }
</style>
```

### Directivas

* **--id**  
Esta directiva genera un identificador para el componente.


### Atributos especiales

* **lang=[less|sassc]**  
Atributo especial que indicara que plantilla utilizar para el preprocesado del estilo. 
Si se omite no aplicará el procesado de ningún sublenguaje de estilos. 

* **scope**  
Aplica el encapsulado mediante un identificador.

## Comportamiento

Para poder crear acciones y comportamientos para nuestro componente se declarará el dentro de la etiqueta de componente:

```
<script scope>
    const el = document.getElementById('--id')
</script>
```

## Comunicación

En la declaración de los componentes se añaden los atributos y valores, estos serán transmitidos a la plantilla del componente como variables Bone.
ej: 
Declaración componente
```
<m-table mycols=3></m-table>
```
Componente m-table
```
<table>
    <ul>
    @for($$mycols)
        <li>
        </li>
    @endfor
    </ul>
</table>

## Ejemplos 

* [Componentes de ejemplo](https://github.com/nestorPons/skeletonPHP/tree/main/src/mycomponents)