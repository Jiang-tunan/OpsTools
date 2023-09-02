/*!
 *  build: Vue  Admin Plus
 *  copyright: vue-admin-beautiful.com
 *  time: 2023-08-28 11:15:16
 */
"use strict";(self["webpackChunkadmin_plus"]=self["webpackChunkadmin_plus"]||[]).push([[8004,4330],{54085:function(e,t,s){s.r(t),s.d(t,{default:function(){return U}});var n=s(64654),i=(s(24549),s(16135),s(49530)),a=(s(80773),s(73210)),r=(s(71085),s(64933)),o=(s(71079),s(53449),s(57658),s(66252)),c=s(2262),u=s(3577),l=s(35513),d=s(69070),v=s(25488),m=s(22076),p=s(22201);const g={key:1,class:"scanning-container"},f={class:"scanning-content"},S={class:"scanning-loading-content"},y={class:"scanning-loading"},D={class:"scanning-text"};var h={__name:"index",setup(e){const t=(0,p.yj)(),s=(0,p.tv)(),h=(0,c.iH)(null),k=(0,o.f3)("$baseMessage"),_=[{name:"扫描设备",type:"primary",key:"scan"}],w=(0,c.qj)({activeStep:Number(t?.query?.step||"1"),loading:!0,sacnData:{delayUnit:"m",status:"0",dchecks:[]},times:"00:00:00",scanResultData:{},isStop:!1,timer:null,countdownTimer:null});function U(){let e=sessionStorage.getItem("druleData")||"{}";if(e=JSON.parse(e),!e.druleids)return I(),sessionStorage.setItem("druleData","{}"),!1;(0,v.ph)({session:e.session,druleids:[e.druleids]}).then((e=>{e.error||(sessionStorage.setItem("druleData","{}"),w.loading=!1,w.isStop=!0,I(),T(),b())})).catch((e=>{console.log("error :>> ",e)}))}function q(){3!==w.activeStep&&w.activeStep++}function I(){1!==w.activeStep&&w.activeStep--}function T(){w.timer&&(clearTimeout(w.timer),w.timer=null)}function b(){w.countdownTimer&&(clearTimeout(w.countdownTimer),w.countdownTimer=null)}function W(e){if(e<1)return b(),!1;const t=(0,m.ZC)(e);w.times=`${t.hour<10?`0${t.hour}`:t.hour}:${t.minute<10?`0${t.minute}`:t.minute}:${t.second<10?`0${t.second}`:t.second}`,w.countdownTimer=setTimeout((()=>{W(e-1)}),1e3)}function $(){let e=sessionStorage.getItem("druleData")||"{}";if(e=JSON.parse(e),!e.druleids)return T(),b(),sessionStorage.setItem("druleData","{}"),s.push({name:"DeviceDiscovery",query:{}}),!1;(0,v.Ic)({druleids:[e.druleids],session:e.session}).then((e=>{console.log("res :>> ",e),"success"===e.response?(b(),W(e.data[0].remain_time),w.scanResultData=e.data[0]||{},w.scanResultData.progress=Number(e.data[0]?.progress?.toFixed()||"0"),w.scanResultData?.progress>=100?(T(),b(),sessionStorage.setItem("druleData","{}"),s.push({query:{step:3}}),q()):w.isStop?(T(),b(),w.isStop=!1):w.timer=setTimeout((()=>{$()}),3e3)):(U(),k("扫描失败请重试","error"))})).catch((e=>{console.log("error :>> ",e)}))}function x(){w.activeStep=1,s.push({query:{step:1}})}return(0,o.YP)((()=>t?.query.step),(e=>{if("2"===e)w.activeStep=2,w.loading=!0,$();else{let e=sessionStorage.getItem("druleData")||"{}";e=JSON.parse(e),e.druleids?(w.activeStep=2,$()):sessionStorage.setItem("druleData","{}")}}),{immediate:!0,deep:!0}),(0,o.bv)((()=>{})),(0,o.Ah)((()=>{T(),b()})),(e,t)=>{const s=r.Gg,v=r.C4,m=a.Xh,p=i.ElButton,k=n.t;return(0,o.wg)(),(0,o.iD)("div",{ref_key:"deviceDiscoveryRef",ref:h,class:"device-discovery-container"},[(0,o.Wm)(v,{class:"device-discovery-step",active:(0,c.SU)(w).activeStep,"align-center":""},{default:(0,o.w5)((()=>[(0,o.Wm)(s,{title:"填写发现信息"}),(0,o.Wm)(s,{title:"开始扫描发现"}),(0,o.Wm)(s,{title:"完成扫描"})])),_:1},8,["active"]),1===(0,c.SU)(w).activeStep?(0,o.WI)(e.$slots,"default",{key:0},(()=>[(0,o.Wm)(l["default"],{class:"discovery-info-container","rules-info-data":(0,c.SU)(w).sacnData,"btn-operation":_},null,8,["rules-info-data"])])):(0,o.kq)("",!0),2===(0,c.SU)(w).activeStep?((0,o.wg)(),(0,o.iD)("div",g,[(0,o._)("div",f,[(0,o._)("div",S,[(0,o.wy)((0,o._)("div",y,null,512),[[k,(0,c.SU)(w).loading]]),(0,o._)("div",D," 扫描中，请稍等（预计耗时："+(0,u.zw)((0,c.SU)(w).times)+"） ",1),(0,o.Wm)(m,{class:"scanning-progress",percentage:(0,c.SU)(w).scanResultData.progress},null,8,["percentage"])]),(0,o.Wm)(p,{onClick:U},{default:(0,o.w5)((()=>[(0,o.Uk)("取消扫描")])),_:1})])])):(0,o.kq)("",!0),3===(0,c.SU)(w).activeStep?((0,o.wg)(),(0,o.j4)(d["default"],{key:2,"device-discovery-ref":(0,c.SU)(h),onCancel:x},null,8,["device-discovery-ref"])):(0,o.kq)("",!0)],512)}}};function k(e){e.__source="src/views/device-discovery/general-discovery/index.vue"}var _=s(83744);"function"===typeof k&&k(h);const w=(0,_.Z)(h,[["__scopeId","data-v-0cf7ec98"]]);var U=w},8665:function(e,t,s){s.r(t),s.d(t,{default:function(){return m}});s(57658);var n=s(2262),i=s(66252),a=s(11369),r=s(54085),o=s(25488),c=s(22201);const u={key:0};var l={__name:"index",setup(e){const t=(0,n.iH)([]),s=(0,c.tv)(),l=(0,n.qj)({loading:!0,isGeneral:!0});function d(){(0,o.tr)({current:1,limit:10,search:{name:""},selectDChecks:"extend"}).then((e=>{l.loading=!1,e.error||(l.isGeneral=!!e.length,t.value=e)})).catch((e=>{console.log("err :>> ",e),l.loading=!1}))}return(0,i.bv)((()=>{let e=sessionStorage.getItem("druleData")||"{}";e=JSON.parse(e),e.session?(l.loading=!1,s.push({name:"GeneralDiscovery",query:{step:2,session:e.session,druleids:e.druleids}})):d()})),(e,s)=>(0,n.SU)(l).loading?(0,i.kq)("",!0):((0,i.wg)(),(0,i.iD)("div",u,[(0,n.SU)(l).isGeneral?((0,i.wg)(),(0,i.j4)(a["default"],{key:0,"rule-data":(0,n.SU)(t)},null,8,["rule-data"])):((0,i.wg)(),(0,i.j4)(r["default"],{key:1}))]))}};function d(e){e.__source="src/views/device-discovery/index.vue"}"function"===typeof d&&d(l);const v=l;var m=v}}]);