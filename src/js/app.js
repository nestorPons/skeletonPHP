// Método get js
let $_GET = {}
document.location.search.replace(/\??(?:([^=]+)=([^&]*)&?)/g, function () {
    function _decode(s) {
        return decodeURIComponent(s.split("+").join(" "));
    }
    $_GET[_decode(arguments[1])] = _decode(arguments[2]);
});

const app = {
    ver: '2.2',
    timeZone: Intl.DateTimeFormat().resolvedOptions().timeZone,
    /**
     * Carga de las vistas
     * @param {String} view 
     * @param {JSON} data datos que se mandan a la vista
     * @param {Bool} load Si refresca o no el contenedor
     * @param {String} container Componente/id/clase donde se carga la vista
     * @param {function} callback 
     */
    getView(view = 'index', data = {}, load = false, container = 'main', callback) {
        let d = {
            'view': view,
            'data': data
        }
        this.ajax('get', d, (html, respond) => {

            if (load) {
                $(container).find('section').hide().end().append(html);
                // Inicializamos el método inicializador del objeto
                if (app[view] != undefined) {
                    if (exist(app[view].load)) 
                        app[view].load();
                    
                }
                this.sections.inicialize(view);
            } else {
                $(container).append(html);
            }
            typeof callback == 'function' && callback(html);
        }, 'html');

    },
    // Peticiones con datos
    post(data, callback, error = true) { // Si esta cargado el navbar poner el spinner en cada petición
        if (typeof navbar != 'undefined') 
            navbar.spinner.show(data.controller);
        

        if (typeof data.controller === 'undefined') {
            this.mens.error({success: false, error: 'No se ha asignado  controlador'})
            return false;
        }
        if (typeof data.db === 'undefined') 
            data.db = $_GET['db'];
        


        this.ajax('post', data, (respond, status, xhr, dataType) => {
            if (typeof navbar != 'undefined') 
                navbar.spinner.hide(data.controller);
            

            let d = null;
            // La respuesta puede ser json o html
            try { // comprobamos si es json
                d = JSON.parse(respond);
                // la respuesta es JSON
                console.log(d);

                // Imprimimos mensaje de error si lo hay
                if (error) 
                    if ((isEmpty(d.success) || d.success == false || d.success == 0) && exist(d.mens)) {
                        console.log('Error en la respuesta!!');
                        this.mens.error(d.mens || 'No se ha podido rehalizar la petición!');
                        return false;
                    }
                

            } catch (e) { // la respuesta es HTML
                html = $(respond);
                this.sections.toggle(html.attr('id'), _ => {

                    html.appendTo('body');
                    html.find('section').each((i, el) => {
                        this.sections.loaded.push(el.id);
                    })

                })
            } finally {
                let resp = d ? d.data : null,
                    state = d && d.mens ? false : true;
                typeof callback == "function" && callback(resp, state);
            }
        });
    },

    ajax(type, data, callback, dataType) {
        const jwt = sessionStorage.getItem('jwt'),
            my_header = (jwt) ? {
                jwt: jwt
            } : {};

        $.ajax({
            url: 'index.php',
            type: type,
            data: data,
            headers: my_header,
            dataType: dataType,
            success: (respond, status, xhr, dataType) => {
                typeof callback == "function" && callback(respond, status, xhr, dataType);
            },
            error: (xhr, status, error) => {
                this.mens.error("Fallo de conexión \n No se pudo enviar los datos");
                typeof callback == "function" && callback(null, 0);
            }
        });
    },
    loadSync(name, callback) {
        let s = document.createElement("script");
        s.onload = callback;
        s.src = name;
        document.querySelector("body").appendChild(s);
    },
    loadAsync(src, callback) {
        if (callback === void 0) {
            callback = null;
        }
        var script = document.createElement('script');
        script.src = src;
        if (callback !== null) {
            if (script.readyState) { // IE, incl. IE9
                script.onreadystatechange = function () {
                    if (script.readyState == "loaded" || script.readyState == "complete") {
                        script.onreadystatechange = null;
                        typeof callback == "function" && callback();
                    }
                };
            } else {
                script.onload = function () {
                    typeof callback == "function" && callback();
                };
            }
        }
        document.getElementsByTagName('body')[0].appendChild(script);
    },
    mens: {
        error(mens) {
            alert('ERROR!! \n' + mens);
            return this;
        },
        confirm(mens) {
            return confirm(mens);
        },
        info(mens) {
            alert(mens);
            return this;
        },
        success(mens) {
            alert(mens);
        }
    },
    sections: {
        active: null,
        last: null,
        loaded: [],
        toggle(section, callback) {
            if ($('section#' + section).is(':visible')) 
                return false;
            


            let $mainSection = $('section');

            if ($('#appadmin').length || $('#appuser').length) {
                $mainSection = $('section').find('section');
            };

            $mainSection.fadeOut('fast');
            $('section#' + section).fadeIn();
            if (typeof callback === 'function') 
                callback();
            

            this.inicialize(section);
        },
        show(section, callback) {
            this.last = this.active;
            // Comprueba que  la seccion existe o no

            if (this.loaded.indexOf(section) != -1) { // Si existe oculta todas menos la solicitada
                app.sections.toggle(section);
                typeof callback == 'function' && callback();
            } else { // Manda una petición para la nueva vista
                app.get({
                    controller: section,
                    action: 'view'
                }, true, fn => { // Registramos la sección
                    this.loaded.push(section);
                    // Activa el evento de inicialización de la sección
                    app.sections.toggle(section);

                    typeof callback == "function" && callback();
                })
            }
            this.exit();
        },
        // Comportamiento de la sección activa al cargarse
        inicialize(section) {

            if (section == 'appadmin') 
                section = 'tpv';
            

            this.active = section;

            let activeZone = app[this.active];

            if (activeZone) { // Cargamos los botones de herramientas
                typeof activeZone.buttons != 'undefined' && typeof activeZone.buttons == 'object' && menu.show(activeZone.buttons);
                // Se cargan
                typeof activeZone.open != 'undefined' && typeof activeZone.open == 'function' && activeZone.open();

                // Carga del título de la sección
                if (menu.tile) 
                    menu.tile.textContent = activeZone.name;
                


            }
        },
        // Comportamiento de los botones de herramientas según la seccion que esté activa
        next() {
            typeof app[this.active].next == 'function' && app[this.active].next();
        },
        prev() {
            typeof app[this.active].prev == 'function' && app[this.active].prev();
        },
        del() {
            typeof app[this.active].del == 'function' && app[this.active].del();
        },
        add() {
            typeof app[this.active].add == 'function' && app[this.active].add();
        },
        print() {
            typeof app[this.active].print == 'function' && app[this.active].print();
        },
        filter() {
            typeof app[this.active].filter == 'function' && app[this.active].filter();
        },
        search() {},
        exit() {
            if (app[this.last] != undefined && typeof app[this.last].exit == 'function') {
                app[this.last].exit(f => {
                    app[this.last].change = false;
                })
            }
        }
    },
    form: { // Verificamo y si es erroneo nos muestra un mensaje con el atributo tile-error o un mensaje por defecto
        verify($this) {
            let type = $this.get(0).tagName,
                _verify = function ($this) {
                    let mens = '',
                        r = true;
                    if ($('#' + $this.attr('for')).val() != $this.val()) {
                        mens = $this.attr('tile-error') || "¡Los campos no coinciden!";
                        r = false;
                    }

                    $this.get(0).setCustomValidity(mens);
                    return r;
                }
            switch (type) {
                case 'INPUT':
                    return _verify($this);
                case 'FORM':
                    // Vrerificamos si es un formulario
                    let success = true;
                    $this.find('.verify').each(function () {
                        if (!_verify($(this))) {
                            $(this).get(0).reportValidity();
                            success = false;
                        }
                    })
                    return success;
            }
        }
    },
    formToObject(form) {
        let obj = {};
        let elements = form.querySelectorAll("input, select, textarea");
        for (let i = 0; i < elements.length; ++ i) {
            var element = elements[i],
                name = element.name,
                value = (element.type == 'checkbox' || element.type == 'radio') ? ((element.checked) ? element.value : element.getAttribute('default') || 0) : element.value;

            if (name) 
                obj[name] = value;
            

        }
        return obj;
    },
    formToJSONString(form) {
        return JSON.stringify(this.formToObject(form));
    },
    clock() {
        momentoActual = new Date();
        hora = momentoActual.getHours();
        minuto = momentoActual.getMinutes();
        segundo = momentoActual.getSeconds();

        str_segundo = new String(segundo);
        if (str_segundo.length == 1) 
            segundo = "0" + segundo;
        


        str_minuto = new String(minuto);
        if (str_minuto.length == 1) 
            minuto = "0" + minuto;
        


        str_hora = new String(hora);
        if (str_hora.length == 1) 
            hora = "0" + hora;
        


        horaImprimible = hora + " : " + minuto;

        $('.clock').val(horaImprimible);

        // setTimeout("app.clock()",1000)
    },
    loadDataToForm(data, form) {
        if (data == undefined) 
            return false;
        

        var els = form.getElementsByTagName('input');
        for (const el of els) {
            if (el.attributes != undefined && el.hasAttribute('name')) {
                if (el.type == 'checkbox') {
                    el.checked = data[el.attributes.name.value] > el.getAttribute('default');
                } else 
                    el.value = data[el.attributes.name.value];
                

            }
        }
        els = form.getElementsByTagName('select');
        for (let i in els) {
            const el = els[i];
            if (el.attributes != undefined) {
                el.value = data[el.attributes.name.value];
                el.classList.add('valid')
            }
        }
        return form;
    },
    help() {
        this.mens.info(`
            TPVOnline 
            v.${
            this.ver
        }
            Autor : Néstor Pons Portolés
            Email : nestorpons@gmail.com
            Licencia : MIT 2019
        `);
    },
    close() { // Eliminamos las zonas abiertas
        $('section:not("#login")').hide().remove();
        $('section#login').show();
        // Quitamos token de autentificación
        sessionStorage.removeItem('jwt');
        // Eliminamos la base de datos
        DB.remove();
    }
}
const DB = {
    storage: [],
    current: 0,
    table: null,
    key(table, key, value) {
        this.get(table).then(d => {});
    },
    // Consultar datos de la bd local
    get(table = this.table, key, value, filter) {
        return new Promise((resolve, reject) => {
            const _equalValues = function (el) {
                const k = (typeof el[key] === 'string') ? el[key].toLowerCase().trim() : el[key],
                    v = (typeof value === 'string') ? value.toLowerCase().trim() : value;

                if (k) 
                    return typeof k === 'number' ? k == v : k.includes(v);
                 else 
                    return false;
                

            }
            if (table == undefined) { // Si no le paso un indice me devuelve todos los nombres de tablas
                resolve(this.storage);
            } else { // Si no se pasan key o value devolvemos todos los registros
                if ((key == undefined || value == undefined) && filter == undefined) 
                    resolve(this.storage[table]);
                 else 
                    resolve(this.storage[table].filter(el => {
                        if (filter) {
                            if (filter.indexOf('==') != -1) {
                                let arr = filter.split('==');
                                return _equalValues(el) && el[arr[0].trim()] == arr[1].trim();
                            } else if (filter.indexOf('>') != -1) {
                                let arr = filter.split('>');
                                return _equalValues(el) && el[arr[0].trim()] > arr[1].trim();
                            } else if (filter.indexOf('<') != -1) {
                                let arr = filter.split('<');
                                return _equalValues(el) && el[arr[0].trim()] < arr[1].trim();
                            };
                        } else 
                            return _equalValues(el);
                        

                    })) || reject(false);
                

            };
        });
    },
    // Añade datos a la tabla
    set(table = this.table, data, key, value) {
        return new Promise((resolve, reject) => {
            if (key) {
                let i = this.storage[table].findIndex(el => {
                    return el[key] == value;
                })
                if (i == -1) 
                    this.storage[table].push(data);
                 else 
                    this.storage[table][i] = data;
                

            } else { // inicializa
                if (typeof this.storage[table] == 'undefined') 
                    this.storage[table] = [];
                

                // Guarda datos en formato array
                for (let i in data) {
                    this.storage[table].push(data[i]);
                }
            }
            // actualiza todos los elementos con data-...
            document.querySelectorAll(`[data-${table}]`).forEach(e => {
                console.log(e);
            })
            resolve(this.storage[table]);
        })
    },
    last(table = this.table) {
        return this.get(table).then(d => d[d['length'] - 1]);
    },
    lastId(table = this.table) {
        return this.get(table).then(d => d[d['length'] - 1].id);
    },
    loadIndex(index) {
        if (this.storage[index] != undefined) {
            this.current = index;
            return this.storage[index];
        };
    },
    async next(table = this.table, id) {
        let last = null;
        const data = await this.get(table);
        // Recorremos el array al revés
        for (let i = data.length - 1; i >= 0; i--) {
            const d = data[i];
            if (d) {
                if (d.id == id) 
                    return last;
                

                last = d;
            } else 
                return false;
            

        }
        return false;
    },
    async prev(table, id) {
        let last = null;
        const data = await this.get(table);

        for (const i in data) {
            const d = data[i];
            if (d.id == id) 
                return last;
            

            last = d;
        }
        return false;
    },
    exist(table = this.table) {
        return typeof this.storage[table] != 'undefined';
    },
    post(controller, action, data) {

        return new Promise((resolve, reject) => { // Guardamos en remoto
            app.post({
                controller: controller,
                action: action,
                data: data
            }, (d, r) => { // Carga de la base de datos en local
                if (r) {
                    if (this.exist(controller)) {
                        const c = {
                            ...data,
                            ...d
                        };
                        this.set(controller, c, 'id', c.id);
                    }
                    resolve(d);
                } else 
                    reject(d);
                

            })
        })
    },
    remove() {
        this.storage = [];
        this.current = 0;
        this.table = null;
    }
}
const date = {
    date: new Date(),
    current() {
        let f = new Date();
        return this.actual() + ' ' + f.getHours() + ':' + f.getMinutes() + ':' + f.getSeconds();
    },
    actual() {
        let f = new Date();
        return(f.getDate() + "/" + (
            f.getMonth() + 1
        ) + "/" + f.getFullYear());
    },
    now(arg = '') {
        let f = new Date(),
            d = f.getDate().toString().padStart(2, '0'),
            m = (f.getMonth() + 1).toString().padStart(2, '0'),
            y = f.getFullYear().toString(),
            h = f.getHours().toString().padStart(2, '0'),
            n = f.getMinutes().toString().padStart(2, '0'),
            s = f.getSeconds().toString().padStart(2, '0');

        switch (arg) {
            case 'sqldate':
                return y + "-" + m + "-" + d;
            case 'date':
                return d + "/" + m + "/" + y;
            case 'hour':
                return h + ":" + n;
            case 'sql':
                return y + '-' + m + '-' + d + ' ' + h + ':' + n + ':' + s;
            default:
                return d + "/" + m + "/" + y + ' ' + h + ":" + n;
        }
    },
    format(date, format) {
        if (date) {
            let d,
                m,
                a,
                h,
                n,
                s;
            if (typeof date === 'string') {
                let f = date.split(' '),
                    fecha = f[0],
                    horario = f[1];

                // Si tiene horas ...
                if (horario) {
                    let x = horario.split(':');
                    h = x[0].padStart(2, '0');
                    min = x[1].padStart(2, '0');
                    s = x[2].padStart(2, '0');
                }
                if (fecha.indexOf("/") > 0) {
                    let arr = fecha.split('/');
                    d = ("0" + arr[0]).slice(-2);
                    m = ("0" + arr[1]).slice(-2);
                    a = arr[2];
                } else if (fecha.indexOf("-") > 0) {
                    let arr = fecha.split('-');
                    d = ("0" + arr[2]).slice(-2);
                    m = ("0" + arr[1]).slice(-2);
                    a = arr[0];
                } else if (fecha.length == 4) {
                    d = fecha.substr(2);
                    m = fecha.substr(0, 2);
                    a = fechaActual('y');
                } else if (fecha.length == 8) {
                    d = fecha.substr(6, 2);
                    m = fecha.substr(4, 2);
                    a = fecha.substr(0, 4);
                }
            } else if (typeof date === 'object') {
                d = date.getDate().toString().padStart(2, '0');
                m = (date.getMonth() + 1).toString().padStart(2, '0');
                a = date.getFullYear().toString();
                h = date.getHours().toString().padStart(2, '0');
                n = date.getMinutes().toString().padStart(2, '0');
                s = date.getSeconds().toString().padStart(2, '0');
            } else 
                return false;
            

            switch (format) {
                case 'sql':
                    return a + '-' + m + '-' + d;
                case 'datetime':
                    return a + '-' + m + '-' + d + ' ' + h + ':' + min + ':' + s;
                case 'short':
                    return d + '/' + m + '/' + a;
                case 'print':
                    return d + '/' + m + '/' + a + ' ' + h + ':' + min + ':' + s;
                case 'md':
                    return m + d;
                case 'id':
                    return a + m + d;
                case 'day':
                    return d;
                case 'month':
                    return m;
                case 'year':
                    return a;
                case 'hour':
                    return h + ':' + min || false;
                case 'long':
                    let month = '';
                    switch (m) {
                        case '1': month = 'Enero';
                            break;
                        case '2': month = 'Febrero';
                            break;
                        case '3': month = 'Marzo';
                            break;
                        case '4': month = 'Abril';
                            break;
                        case '5': month = 'Mayo';
                            break;
                        case '6': month = 'Junio';
                            break;
                        case '7': month = 'Julio';
                            break;
                        case '8': month = 'Agosto';
                            break;
                        case '9': month = 'Septiembre';
                            break;
                        case '10': month = 'Octubre';
                            break;
                        case '11': month = 'Noviembre';
                            break;
                        case '12': month = 'Diciembre';
                            break;
                    }
                    return `${d} de ${month} del ${a}`;

                default:
                    return new Date(a, m - 1, d, h, min, s);
            }
        }
        return null;
    },
    diff(f1, f2) {

        let d1 = new Date(this.format(f1, 'sql')).getTime(),
            d2 = new Date(this.format(f2, 'sql')).getTime(),
            diff = d2 - d1;

        return(diff / (1000 * 60 * 60 * 24));
    },
    add(argdate, value = 1, unity = 'days', format = null) {

        const date = (typeof argdate !== 'object') ? new Date(this.format(argdate, 'sql')) : argdate;
        const v = parseInt(value);

        switch (unity) {
            case 'days': date.setDate(date.getDate() + v);
                break;
            case 'month': date.setMonth(date.getMonth() + v);
                break;
            case 'year': date.setFullYear(date.getFullYear() + v);
        }
        if (format) 
            return this.format(date, format);
         else 
            return date;
        

    },
    sql(param = this.date) {
        return this.format(param, 'sql');
    },
    short(param = this.date) {
        return this.format(param, 'short');
    },
    hour(param = this.date) {
        return this.format(param, 'hour');
    },
    datetime(param = this.date) {
        return this.format(param, 'datetime');
    }
}
// Funciones para desarrollo
var echo = function () {
    for (let i in arguments) 
        console.log(arguments[i]);
};
var exist = function (arg = undefined) {
    return arg != undefined && arg != null
}
var remove = function (arr =[]) {
    do {
        let b = arr[0]
        if (b) 
            b.remove()
        
    } while (arr.length > 0)
}
var isset = function (arg = undefined) {
    return typeof arg == undefined || arg == null || arg == false || arg == 0
}
var isEmpty = function (arg = undefined) {
    return typeof arg == undefined || arg == null || arg == false || arg == 0 || arg == ''
}
var isTrue = function (arg = null) {
    return arg === true
}

