/*!
 *  build: Vue  Admin Plus
 *  copyright: vue-admin-beautiful.com
 *  time: 2023-08-28 11:15:16
 */
"use strict";(self["webpackChunkadmin_plus"]=self["webpackChunkadmin_plus"]||[]).push([[5753],{22076:function(e,n,t){function r(e){const n=document.documentElement.clientWidth;return n>=1440?16*e:e*Math.round(n/1440*16)}function o(e,n,t){let o=0;return o="remToPx"===t?r(n):n,e?.value?.offsetHeight?e.value.offsetHeight-o:e?.offsetHeight?e.offsetHeight-o:void 0}function i(e,n){const t=new Map;for(const r of e)t.has(r[n])||t.set(r[n],r);return[...t.values()]}function u(){return"xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx".replace(/[xy]/g,(function(e){const n=16*Math.random()|0,t="x"==e?n:3&n|8;return t.toString(16)}))}function a(e){let n=parseInt(e),t=0,r=0;return n>60&&(t=parseInt(n/60),n=parseInt(n%60),t>60&&(r=parseInt(t/60),t=parseInt(t%60))),{second:n,minute:t,hour:r}}t.d(n,{BE:function(){return u},NC:function(){return o},Nb:function(){return i},ZC:function(){return a}})},4145:function(e,n,t){t.r(n),t.d(n,{default:function(){return m}});var r=t(64654),o=(t(24549),t(16135),t(66252)),i=t(2262),u=t(83798),a=t(22076),s=t(58109),c=t(47720);const f=["src","height"],d=15;var x={__name:"index",setup(e){const n=(0,i.iH)(0),t=(0,i.iH)(null),x=(0,i.iH)(null),l=(0,i.iH)(!0),v=Math.floor(Date.now()/1e3),h=v-v%600,m=h-60*d,p=(0,s.F)(),{theme:w}=(0,c.Jk)(p),g=(0,i.iH)("");function _(){g.value=`${u.chartURL}/d/dc189952-9dfa-440e-b0f6-f3b4dafa79d1/55uR5o6n5qaC6KeI?orgId=1&from=${m}&to=${h}&kiosk&theme=${w.value.themeType}`}(0,o.YP)((()=>w.value.themeType),(()=>{_()}),{immediate:!0,deep:!0});const k=()=>{n.value=(0,a.NC)(t,.2,"remToPx")};return(0,o.wF)((()=>{window.addEventListener("resize",k)})),(0,o.Ah)((()=>{window.removeEventListener("resize",k)})),(0,o.bv)((()=>{k(),l.value=!1;const e=x.value.contentWindow.document;e.classList&&e.classList.remove("grid-drag-cancel"),x.value.onload=function(){x.value.contentWindow.postMessage({showMenu:!1},"*")}})),(e,u)=>{const a=r.t;return(0,o.wy)(((0,o.wg)(),(0,o.iD)("div",{ref_key:"overviewRef",ref:t,class:"overview-container"},[(0,o._)("iframe",{ref_key:"iframeRef",ref:x,src:(0,i.SU)(g),width:"100%",height:(0,i.SU)(n),frameborder:"0"},null,8,f)])),[[a,(0,i.SU)(l)]])}}};function l(e){e.__source="src/views/monitoring-center/monitoring-overview/index.vue"}var v=t(83744);"function"===typeof l&&l(x);const h=(0,v.Z)(x,[["__scopeId","data-v-9361359c"]]);var m=h}}]);