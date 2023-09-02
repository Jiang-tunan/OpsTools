/*!
 *  build: Vue  Admin Plus
 *  copyright: vue-admin-beautiful.com
 *  time: 2023-08-28 11:15:16
 */
"use strict";(self["webpackChunkadmin_plus"]=self["webpackChunkadmin_plus"]||[]).push([[2433],{72433:function(e,l,r){r.r(l),r.d(l,{default:function(){return _}});var o=r(21211),a=(r(24549),r(12791),r(49530)),u=(r(80773),r(47301)),t=(r(52783),r(6715),r(57847)),d=(r(43872),r(66252)),p=r(2262),i=r(49963),s=r(25498),m=r(7605),n={__name:"GroupForm",props:{groupData:{type:Object,default:()=>({})},isSave:{type:Boolean,default:!1}},emits:["save"],setup(e,{expose:l,emit:r}){const n=e,c=(0,p.iH)(null),f=(e,l,r)=>{l?(0,s.CB)(l)?r():r(new Error((0,m.Iu)("请输入正确的Webhook 地址"))):r(new Error((0,m.Iu)("请输入Webhook 地址")))},b=(e,l,r)=>{l?isIP(l)?r():r(new Error((0,m.Iu)("请输入正确的地址"))):r()},_=(e,l,r)=>{l?(0,s.kM)(l)?r():r(new Error((0,m.Iu)("请输入正确的端口"))):r()},y={group:[{required:!0,message:"请输入接收群组",trigger:"blur"}],url:[{required:!0,message:"请输入Webhook 地址",trigger:"blur"},{validator:f,trigger:"blur"}],proxy_address:[{validator:b,trigger:"blur"}],proxy_port:[{validator:_,trigger:"blur"}]},{isSave:g,groupData:v}=(0,p.BK)(n);function U(){c.value.validate((e=>{e&&r("save",v.value)}))}return l({GroupFormRef:c}),(e,l)=>{const r=t.EZ,s=o.nH,m=u.OX,n=a.ElButton,f=o.ly;return(0,d.wg)(),(0,d.j4)(f,{ref_key:"GroupFormRef",ref:c,rules:y,class:"group-form notification-mode-form","label-position":"top",model:(0,p.SU)(v),onSubmit:l[5]||(l[5]=(0,i.iM)((()=>{}),["prevent"]))},{default:(0,d.w5)((()=>[(0,d.Wm)(s,{label:"接收群组",prop:"group"},{default:(0,d.w5)((()=>[(0,d.Wm)(r,{modelValue:(0,p.SU)(v).group,"onUpdate:modelValue":l[0]||(l[0]=e=>(0,p.SU)(v).group=e),modelModifiers:{trim:!0},placeholder:"请输入",class:"input-select",clearable:""},null,8,["modelValue"])])),_:1}),(0,d.Wm)(s,{label:"Webhook 地址",prop:"url"},{default:(0,d.w5)((()=>[(0,d.Wm)(r,{modelValue:(0,p.SU)(v).url,"onUpdate:modelValue":l[1]||(l[1]=e=>(0,p.SU)(v).url=e),modelModifiers:{trim:!0},placeholder:"请输入",class:"input-select",clearable:""},null,8,["modelValue"])])),_:1}),(0,d.Wm)(s,{label:"使用代理服务器",prop:"isproxy"},{default:(0,d.w5)((()=>[(0,d.Wm)(m,{modelValue:(0,p.SU)(v).isproxy,"onUpdate:modelValue":l[2]||(l[2]=e=>(0,p.SU)(v).isproxy=e),"active-value":!0,"inactive-value":!1},null,8,["modelValue"])])),_:1}),(0,d.Wm)(s,{class:"proxy-item"},{default:(0,d.w5)((()=>[(0,d.Wm)(s,{label:"地址",prop:"proxy_address"},{default:(0,d.w5)((()=>[(0,d.Wm)(r,{modelValue:(0,p.SU)(v).proxy_address,"onUpdate:modelValue":l[3]||(l[3]=e=>(0,p.SU)(v).proxy_address=e),modelModifiers:{trim:!0},placeholder:"例如：192.168.1.1",disabled:!(0,p.SU)(v).isproxy,clearable:""},null,8,["modelValue","disabled"])])),_:1}),(0,d.Wm)(s,{class:"port-item",label:"端口",prop:"proxy_port"},{default:(0,d.w5)((()=>[(0,d.Wm)(r,{modelValue:(0,p.SU)(v).proxy_port,"onUpdate:modelValue":l[4]||(l[4]=e=>(0,p.SU)(v).proxy_port=e),modelModifiers:{trim:!0},placeholder:"请输入",disabled:!(0,p.SU)(v).isproxy,clearable:""},null,8,["modelValue","disabled"])])),_:1})])),_:1}),(0,p.SU)(g)?((0,d.wg)(),(0,d.j4)(s,{key:0},{default:(0,d.w5)((()=>[(0,d.Wm)(n,{type:"primary",onClick:U},{default:(0,d.w5)((()=>[(0,d.Uk)("保存并提交")])),_:1})])),_:1})):(0,d.kq)("",!0)])),_:1},8,["model"])}}};function c(e){e.__source="src/views/problem/component/GroupForm.vue"}var f=r(83744);"function"===typeof c&&c(n);const b=(0,f.Z)(n,[["__scopeId","data-v-3d8fe51c"]]);var _=b}}]);