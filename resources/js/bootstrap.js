import axios from 'axios';
import * as bootstrap from 'bootstrap'
import retina from 'retinajs'
import hljs from 'highlight.js'

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Retina.js
retina()

// コードハイライト
Array.from(document.querySelectorAll('pre code')).forEach(code => hljs.highlightElement(code));
