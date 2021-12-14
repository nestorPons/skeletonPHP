<?php namespace FOLDER;

// Carpetas
const PUBLIC_FOLDER_ROOT = 'htdocs/';
const APP = ROOT . 'app/'; 
const SRC = ROOT . 'src/';
const PUBLIC_FOLDER = 'www/'; 

const CORE          = APP . 'core/';
const CONFIG        = APP . 'config/';
const DB            = APP . 'db/';
const CACHES        = APP . 'caches/';
const HELPERS       = APP . 'helpers/';
const MODELS        = SRC . 'models/';
const CONTROLLERS   = SRC . 'controllers/';
const VIEWS         = SRC . 'views/';
const JS            = SRC . 'js/';
const IMG           = SRC . 'img/';
const COMPONENTS  = SRC . 'components/';
const STYLES        = SRC . 'styles/';


namespace FILE; 
const CONFIG     = \FOLDER\CONFIG . 'config.ini';
const BUNDLE_JS  = \FOLDER\PUBLIC_FOLDER . 'bundle.js';
const BUNDLE_CSS = \FOLDER\PUBLIC_FOLDER . 'bundle.css';

namespace CACHE;
const VIEWS = \FOLDER\CACHES . 'cache_views.ini';
