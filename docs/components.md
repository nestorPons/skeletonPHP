# Componentes

### Descripción
Los componentes son objetos encapsulados con una estructura HTML , un diseño CSS, y un comportamiento con JS. 
Son facilmente reutilizables, minimizan y homogenizan todo muestro código. 

Para un mayor dinamismo cuenta con distintas tecnologías para darle mayor funcionalidad a los mismos: 

![ESQUEMA](img/Sistemacomponentes.png "Esquema del SkeletonPHP")

### Ejemplo botón
Ejemplo de un componente botón:  
``<m-btn onclick="alert(`Hay que poner estas comillas para que lo reconozca`)"></m-btn>``

`src/mycomponents/m-btn.phtml`
```
<component id="--id"> 
    <button type = "submit" id="--id_el"  class="$$class collapse tertiary" placeholder=" "
            name = "$$name" tile = "$$tile"
            value = "$$value" tabindex = "$$tabindex" onfocus = "$$onfocus" onblur = "$$onblur" onclick = "$$onclick"
            onkeypress = "$$onkeypress" onkeydown = "$$onkeydown" onkeyup = "$$onkeyup"
            onchange = "$$onchange" $$require $$disabled $$readonly $$checked  
        >
        @if ($$spinner) <i class="spinner hidden"></i> @endif
        @if ($$icon) <i class="lnr lnr-$$icon"></i> @endif
        @if ($$caption) 
            <span class="caption">$$caption</span> 
        @else
            <span class="caption">Aceptar</span> 
        @endif
        
    </button>
</component>
```
Procesado de la salida al archivo .php:  
`<?php $c = new \core\Component('m-btn-success', '[]', ''); $c->print();?>`

Resultado html:  
```<button type="submit" id="tag619cca9846628_el" class=" collapse tertiary" placeholder=" ">
    <span class="caption">Aceptar</span> 
</button>
```