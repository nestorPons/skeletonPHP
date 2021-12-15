# Peticiones GET

## Descripción
Skeleton tiene una peculiaridad en la navegación por su contenido. Dotando a tan singular método de velocidad de carga mediante la reutilización de codigo. 

### Carga de segmentos modulares
Las peticiones entre paginas son hechas mediante app.getView('page.phtml').
Las peticiones son demandadas mediante ajax y se cargan en el main de la pagina principal. Con esto se consigue un aumento significativo en la velocidad de carga al no tener que cargar reiteradamente archivos externos y cabeceras inecesarias. 

![ESQUEMA](img/framework.png "Esquema de SkeletonPHP")

ej: 
```
data = {"key":"value"}
app.getView('page.phtml', data,  'body', true);
```
Con este comando estamos realizando una petición a la pagina 'page.phtml', mandandole unos valores que podremos interpretar mediante el sublenguje php Bone como variables $$nombre_de_la_variable. 
Le estamos diciendo que lo cargue en el body de la pagina principal y que oculte el contenido (IMPORTANTE! Oculta y no destruye, aumentando la velocidad de recuperación de la pagina).
