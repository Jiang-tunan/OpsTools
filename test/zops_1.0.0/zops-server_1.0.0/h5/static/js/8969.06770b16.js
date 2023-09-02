/*!
 *  build: Vue  Admin Plus
 *  copyright: vue-admin-beautiful.com
 *  time: 2023-08-28 11:15:16
 */
"use strict";(self["webpackChunkadmin_plus"]=self["webpackChunkadmin_plus"]||[]).push([[8969],{32134:function(e,a,n){n.d(a,{IP:function(){return c},MB:function(){return i},PN:function(){return o},YZ:function(){return l},iv:function(){return u},qb:function(){return s},tS:function(){return d},vP:function(){return r}});var t=n(52806);function i(e){return(0,t.Z)({params:{method:"idc.get",data:e}})}function c(e){return(0,t.Z)({params:{method:"idc.create",data:e}})}function d(e){return(0,t.Z)({params:{method:"idc.update",data:e}})}function o(e){return(0,t.Z)({params:{method:"idc.delete",data:e}})}function l(e){return(0,t.Z)({params:{method:"house.get",data:e}})}function r(e){return(0,t.Z)({params:{method:"house.create",data:e}})}function u(e){return(0,t.Z)({params:{method:"house.update",data:e}})}function s(e){return(0,t.Z)({params:{method:"house.delete",data:e}})}},16992:function(e,a,n){n.r(a),n.d(a,{default:function(){return _}});var t=n(64654),i=(n(24549),n(16135),n(59109)),c=(n(65195),n(31574)),d=(n(72698),n(72673),n(56864),n(50952)),o=(n(83646),n(49530)),l=(n(80773),n(57847)),r=(n(43872),n(66252)),u=n(2262),s=n(49963),m=n(3577),f=n(65781),p=n(32134);const k={class:"idc-content"},h={class:"search-content"},b={class:"custom-tree-node"},C={key:1},v={class:"el-dropdown-link"};var D={__name:"DataCenter",emits:["idcNodeClick"],setup(e,{emit:a}){const D=(0,r.f3)("$baseConfirm"),g=(0,r.f3)("$baseMessage"),w=(0,r.RC)((()=>Promise.all([n.e(922),n.e(4289)]).then(n.bind(n,85752)))),y=(0,u.iH)(null),_={children:"children",label:"name",id:"idcid"},I=(0,u.iH)({hashosts:!1,search:{name:""}}),W=(0,u.qj)({loading:!0,idcName:"",backupData:{},backupName:{},currentKey:"",idcData:[],idcInfoData:{visible:!1,title:"新建数据中心（IDC）",type:"new",data:{name:"",userid:"",address:"",usrgrpid:""}}}),N=e=>{y.value.filter(e)},Z=()=>{W.idcInfoData={visible:!0,title:"新建数据中心（IDC）",type:"new",data:{name:"",userid:"",address:"",usrgrpid:""}}},R=(e,a)=>!e||a.name.includes(e),U=()=>{W.loading=!0,(0,p.MB)(I.value).then((e=>{W.loading=!1,W.idcData=e,!e.error&&e.length&&(W.currentKey=e[0]?.idcid,y.value.setCurrentKey(e[0]?.idcid),a("idcNodeClick",e[0]))})).catch((e=>{W.loading=!1,console.log("err :>> ",e)}))};function V(){W.idcInfoData.visible=!1}function M(e){a("idcNodeClick",e)}function K(e){e.isRename=!0,W.backupName=e.name,W.backupData=e}function P(e){(0,p.tS)({name:e.name,idcid:e.idcid}).then((e=>{e.error?(W.backupData.isRename=!1,e.name=W.backupName):W.backupData.isRename=!1})).catch((()=>{e.isRename=!1,e.name=W.backupName}))}function S(e){W.backupData.isRename=!1,e.name=W.backupName}function j(e){W.idcInfoData={visible:!0,title:"编辑数据中心（IDC）",type:"edit",data:{name:e.name,userid:e.userid,address:e.address,idcid:e.idcid,usrgrpid:e.usrgrpid}}}function q(e){if(!e.idcid)return!1;D(`删除数据中心后不可恢复，确定要删除“${e.name}”吗？`,"删除数据中心","delete",(async()=>{(0,p.PN)([e.idcid]).then((e=>{e.error||(g("删除成功","success"),U())})).catch((e=>{console.log("err :>> ",e)}))}))}return(0,r.bv)((()=>{U()})),(e,a)=>{const n=l.EZ,p=(0,r.up)("vab-icon"),D=o.ElButton,g=d.Q0,B=c.Df,$=c.o2,x=c.YK,E=i.y,H=t.t;return(0,r.wg)(),(0,r.iD)("div",k,[(0,r._)("div",h,[(0,r.Wm)(n,{modelValue:I.value.search.name,"onUpdate:modelValue":a[0]||(a[0]=e=>I.value.search.name=e),placeholder:"请输入关键字搜索",clearable:"","prefix-icon":(0,u.SU)(f.Search),onChange:N},null,8,["modelValue","prefix-icon"]),(0,r.Wm)(g,{content:"新增数据中心",placement:"top",effect:"light"},{default:(0,r.w5)((()=>[(0,r.Wm)(D,{class:"add-btn",onClick:Z},{default:(0,r.w5)((()=>[(0,r.Wm)(p,{icon:"add-line"})])),_:1})])),_:1})]),(0,r.wy)(((0,r.wg)(),(0,r.j4)(E,{ref_key:"idcRef",ref:y,class:"idc-tree",data:W.idcData,"current-node-key":W.currentKey,"node-key":"idcid","highlight-current":"",props:_,"filter-node-method":R,onNodeClick:M},{default:(0,r.w5)((({node:e,data:t})=>[(0,r._)("span",b,[t.isRename?((0,r.wg)(),(0,r.iD)("div",{key:0,class:"rename-label",onClick:a[1]||(a[1]=(0,s.iM)((()=>{}),["stop"]))},[(0,r.Wm)(n,{modelValue:t.name,"onUpdate:modelValue":e=>t.name=e,placeholder:"请输入数据中心名称",clearable:""},null,8,["modelValue","onUpdate:modelValue"]),(0,r.Wm)(p,{class:"rename-icon icon-check",icon:"check-fill",onClick:(0,s.iM)((e=>P(t)),["stop"])},null,8,["onClick"]),(0,r.Wm)(p,{class:"rename-icon icon-close",icon:"close-fill",onClick:(0,s.iM)((e=>S(t)),["stop"])},null,8,["onClick"])])):((0,r.wg)(),(0,r.iD)("span",C,(0,m.zw)(e.label),1)),(0,r.Wm)(x,null,{dropdown:(0,r.w5)((()=>[(0,r.Wm)($,null,{default:(0,r.w5)((()=>[(0,r.Wm)(B,{onClick:e=>K(t)},{default:(0,r.w5)((()=>[(0,r.Uk)(" 重命名 ")])),_:2},1032,["onClick"]),(0,r.Wm)(B,{onClick:e=>j(t)},{default:(0,r.w5)((()=>[(0,r.Uk)(" 编辑 ")])),_:2},1032,["onClick"]),(0,r.Wm)(B,{onClick:e=>q(t)},{default:(0,r.w5)((()=>[(0,r.Uk)(" 删除 ")])),_:2},1032,["onClick"])])),_:2},1024)])),default:(0,r.w5)((()=>[(0,r._)("span",v,[(0,r.Wm)(p,{icon:"more-2-fill"})])])),_:2},1024)])])),_:1},8,["data","current-node-key"])),[[H,W.loading]]),W.idcInfoData.visible?((0,r.wg)(),(0,r.j4)((0,u.SU)(w),{key:0,"idc-info-data":W.idcInfoData.data,title:W.idcInfoData.title,type:W.idcInfoData.type,visible:W.idcInfoData.visible,onCancel:V,onConfirm:U},null,8,["idc-info-data","title","type","visible"])):(0,r.kq)("",!0)])}}};function g(e){e.__source="src/views/system/idc/DataCenter.vue"}var w=n(83744);"function"===typeof g&&g(D);const y=(0,w.Z)(D,[["__scopeId","data-v-14b8a82b"]]);var _=y}}]);