import PostManager from './post-manager';
import A2HS from './a2hs';
import ServiceWorkerInit from './service-worker-init';

let a2hs = new A2HS();
let postManager = new PostManager();
let serviceWorkerInit = new ServiceWorkerInit();

a2hs.start();
serviceWorkerInit.start();
postManager.start();