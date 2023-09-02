/*!
 *  build: Vue  Admin Plus
 *  copyright: vue-admin-beautiful.com
 *  time: 2023-08-28 11:15:16
 */
"use strict";(self["webpackChunkadmin_plus"]=self["webpackChunkadmin_plus"]||[]).push([[1344,9527,211],{18907:function(e,t,n){n.d(t,{KW:function(){return u},Kj:function(){return m},Ld:function(){return i},Ob:function(){return p},Ox:function(){return l},Wh:function(){return s},XF:function(){return o},bm:function(){return d},uX:function(){return a},v0:function(){return c},y2:function(){return f}});var r=n(52806);function o(e){return(0,r.Z)({params:{method:"host.get",data:e}})}function a(e){return(0,r.Z)({params:{method:"host.exportinventory",data:e}})}function u(e){return(0,r.Z)({params:{method:"host.update",data:e}})}function i(e){return(0,r.Z)({params:{method:"host.create",data:e}})}function d(e){return(0,r.Z)({params:{method:"host.discoverupdate",data:e}})}function l(e){return(0,r.Z)({params:{method:"host.delete",data:e}})}function c(e){return(0,r.Z)({params:{method:"hostgroup.get",data:e}})}function s(e){return(0,r.Z)({params:{method:"modeltype.create",data:e}})}function p(e){return(0,r.Z)({params:{method:"modeltype.get",data:e}})}function f(e){return(0,r.Z)({params:{method:"modeltype.update",data:e}})}function m(e){return(0,r.Z)({params:{method:"modeltype.delete",data:e}})}},94577:function(e,t,n){n.d(t,{Nl:function(){return a},Oc:function(){return i},Rt:function(){return u},_T:function(){return o}});var r=n(52806);function o(e){return(0,r.Z)({params:{method:"inventorytype.get",data:e}})}function a(e){return(0,r.Z)({params:{method:"inventorytype.create",data:e}})}function u(e){return(0,r.Z)({params:{method:"inventorytype.update",data:e}})}function i(e){return(0,r.Z)({params:{method:"inventorytype.delete",data:e}})}},9686:function(e,t,n){n.r(t),n.d(t,{default:function(){return v}});var r=n(49530),o=(n(24549),n(80773),n(21211)),a=(n(12791),n(6715),n(53202)),u=(n(82506),n(57658),n(66252)),i=n(2262),d=n(49963),l=n(39049),c=n(94577),s=n(18907),p={__name:"MoveAssetTypeInfo",props:{moveInfoData:{type:Object,default:()=>({})},title:{type:String,default:"移动资产"},visible:{type:Boolean,default:!1}},emits:["cancel","confirm"],setup(e,{emit:t}){const n=e,p={inventory_typeid:[{required:!0,message:"请选择资产类型",trigger:"blur"}]},f=(0,u.f3)("$baseMessage"),m=(0,i.iH)(null),y=(0,i.iH)([]),v={isLeaf:e=>!e.hasson,label:"name",id:"inventory_typeid"},{title:h,visible:_,moveInfoData:g}=(0,i.BK)(n),Z=(e,t)=>{(0,c._T)({fid:e?.data?.inventory_typeid||"0"}).then((e=>{!e.error&&e.length&&t(e)})).catch((e=>{console.log("err :>> ",e)}))};function b(){t("cancel")}function k(){m.value.validate((e=>{if(e){const e=[];g.value.hostids.forEach((t=>{e.push({hostid:t,inventory_typeid:g.value.inventory_typeid})})),(0,s.KW)(e).then((e=>{e.error||(f("移动资产成功","success"),t("cancel"),t("confirm"))}))}}))}function w(){(0,c._T)({inventory_typeids:[g.value.inventory_typeid]}).then((e=>{!e.error&&e.length&&(y.value=e[0].fathers)})).catch((e=>{console.log("err :>> ",e)}))}return(0,u.bv)((()=>{g.value.inventory_typeid&&w()})),(e,t)=>{const n=a.i,c=o.nH,s=o.ly,f=r.ElButton;return(0,u.wg)(),(0,u.j4)((0,i.SU)(l["default"]),{modelValue:(0,i.SU)(_),"onUpdate:modelValue":[t[2]||(t[2]=e=>(0,i.dq)(_)?_.value=e:null),b],title:(0,i.SU)(h),width:"30rem"},{footer:(0,u.w5)((()=>[(0,u.Wm)(f,{onClick:b},{default:(0,u.w5)((()=>[(0,u.Uk)("取消")])),_:1}),(0,u.Wm)(f,{type:"primary",onClick:k},{default:(0,u.w5)((()=>[(0,u.Uk)("确定")])),_:1})])),default:(0,u.w5)((()=>[(0,u.Wm)(s,{ref_key:"inventoryFormRef",ref:m,"label-position":"top",model:(0,i.SU)(g),rules:p,onSubmit:t[1]||(t[1]=(0,d.iM)((()=>{}),["prevent"]))},{default:(0,u.w5)((()=>[(0,u.Wm)(c,{label:"选择资产类型",prop:"inventory_typeid"},{default:(0,u.w5)((()=>[(0,u.Wm)(n,{modelValue:(0,i.SU)(g).inventory_typeid,"onUpdate:modelValue":t[0]||(t[0]=e=>(0,i.SU)(g).inventory_typeid=e),class:"input-select","check-strictly":"","default-expanded-keys":(0,i.SU)(y),lazy:"",props:v,"node-key":"inventory_typeid",load:Z,"render-after-expand":!1},null,8,["modelValue","default-expanded-keys"])])),_:1})])),_:1},8,["model"])])),_:1},8,["modelValue","title"])}}};function f(e){e.__source="src/views/assets-management/components/MoveAssetTypeInfo.vue"}var m=n(83744);"function"===typeof f&&f(p);const y=(0,m.Z)(p,[["__scopeId","data-v-3513b3a4"]]);var v=y}}]);