!function(e){var t={};function s(i){if(t[i])return t[i].exports;var o=t[i]={i:i,l:!1,exports:{}};return e[i].call(o.exports,o,o.exports,s),o.l=!0,o.exports}s.m=e,s.c=t,s.d=function(e,t,i){s.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:i})},s.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},s.t=function(e,t){if(1&t&&(e=s(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var i=Object.create(null);if(s.r(i),Object.defineProperty(i,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var o in e)s.d(i,o,function(t){return e[t]}.bind(null,o));return i},s.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return s.d(t,"a",t),t},s.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},s.p="",s(s.s=0)}([function(e,t,s){"use strict";s.r(t);class i{constructor(e){this.postManager=e}addEventListener(){document.addEventListener("DOMContentLoaded",function(){document.querySelector(".page__content").addEventListener("scroll",function(e){e.target.scrollTop>e.target.scrollHeight-e.target.clientHeight-500&&window.setTimeout(function(){this.postManager.fetchNextPosts()}.bind(this),500)}.bind(this))}.bind(this))}}let o=new class{start(){let e;window.addEventListener("beforeinstallprompt",s=>{s.preventDefault(),e=s,t.style.display="block",t.addEventListener("click",s=>{t.style.display="none",e.prompt(),e.userChoice.then(t=>{"accepted"===t.outcome?console.log("User accepted the A2HS prompt"):console.log("User dismissed the A2HS prompt"),e=null})})});const t=document.querySelector(".add-button");t.style.display="none"}},n=new class{constructor(){this.postManagerScroll=new i(this),this.postManagerScroll.addEventListener(),this.lastReceivedTimeStamp=0,this.nextTimestampToRequest=0,this.receivedTimestamps={},this.requestedTimestamps={},this.plyrIdCounter=1}start(){document.addEventListener("DOMContentLoaded",function(){this.fetchNextPosts()}.bind(this))}fetchNextPosts(){var e=this.nextTimestampToRequest;isNaN(this.requestedTimestamps[this.nextTimestampToRequest])&&(this.requestedTimestamps[this.nextTimestampToRequest]=this.nextTimestampToRequest,this.nextTimestampToRequest>0&&$("#bottom-loading-modal").show(),$.get({url:"/app02/posts/chihuahua,chihuahuas,chihuahualife,chihuahualove,chihuahuaworld,chihuahualovers,chihuahuasofinstagram,chihuahuastagram,chihuahualover/"+this.nextTimestampToRequest,success:function(t){this.lastReceivedTimeStamp=t[t.length-1].timestamp,isNaN(this.receivedTimestamps[e])&&(this.renderPosts(t),this.receivedTimestamps[e]=e,this.nextTimestampToRequest=this.lastReceivedTimeStamp+1,$("#main-loading-modal").hide(),$("#bottom-loading-modal").hide())}.bind(this),dataType:"json"}))}renderPosts(e){var t="",s=[];for(let i=0;i<e.length;++i){if(t=this.renderPost(e[i],s),$("#post_list").append(t),s.length>0)for(let e=0;e<s.length;++e)this.initPlyr(s[e]);0}}initPlyr(e){new Plyr("#"+e).on("ready",e=>{var t=e.detail.plyr.elements.buttons.play;for(let e=0;e<t.length;++e){var s=$(t[e]);if(s.hasClass("plyr__control--overlaid"))s.find("svg[role=presentation]").attr("viewBox","0 0 30 30")}})}renderPost(e,t){var s,i=0,o=new Date(1e3*e.timestamp).toLocaleString(),n="",r="";if(e.photos)for(i=0;i<e.photos.length;++i)r+='<img src="'+e.photos[i].url+'"/><br/>';if(e.text&&(n=e.text),e.videos){var a=e.videos.embedCode.match(/<video.*id=["'](\S+)["']/);a&&a.length>0&&(s=a[1]),s||(s="plyr-id-"+this.plyrIdCounter++,e.videos.embedCode=e.videos.embedCode.replace("<video",'<video id="'+s+'"')),e.videos.embedCode=e.videos.embedCode.replace("<video",'<video id="'+s+'"'),t.push(s),n+='<div class="video-container">'+e.videos.embedCode+"</div>"}return r&&(n+=r),'<ons-list-item>    <ons-card>       <div class="post-container">        <div class="title">From '+e.blogger+" at "+o+'</div>        <div class="card__content">'+n+"</div>       </div>    </ons-card></ons-list-item>"}},r=new class{start(){"serviceWorker"in navigator&&navigator.serviceWorker.register("/sw.js").then(()=>console.log("Service Worker Registered")).catch(e=>console.log(e))}};o.start(),r.start(),n.start()}]);