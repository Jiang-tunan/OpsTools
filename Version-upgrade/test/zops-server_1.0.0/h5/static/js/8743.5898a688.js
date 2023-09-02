/*!
 *  build: Vue  Admin Plus
 *  copyright: vue-admin-beautiful.com
 *  time: 2023-08-28 11:15:16
 */
"use strict";(self["webpackChunkadmin_plus"]=self["webpackChunkadmin_plus"]||[]).push([[8743],{5743:function(e,t,a){a.r(t),a.d(t,{default:function(){return f}});var o=a(64654),l=(a(24549),a(16135),a(91067)),n=(a(78526),a(47546)),i=(a(49653),a(37965)),s=(a(82880),a(60717),a(66252)),r=a(3577),p=a(2262),u={__name:"CustomTable",props:{columnList:{type:Array,default:()=>[]},listData:{type:Array,default:()=>[]},layout:{type:String,default:"total, sizes, prev, pager, next, jumper"},total:{type:Number,default:0},pageNo:{type:Number,default:0},pageSize:{type:Number,default:20},maxHeight:{type:[String,Number],default:"auto"},className:{type:String,default:""},idkey:{type:String,default:""},listLoading:{type:Boolean,default:!0},isIndex:{type:Boolean,default:!1},isSelection:{type:Boolean,default:!0},isPagination:{type:Boolean,default:!0},stripe:{type:Boolean,default:!0},spanMethod:{type:Function,default:()=>{}}},emits:["sizeChange","currentChange","selectRows","sortChange","spanMethod"],setup(e,{expose:t,emit:a}){const u=(0,p.iH)(null),d=e=>{a("sizeChange",e)},g=e=>{a("currentChange",e)},c=e=>{a("selectRows",e)},f=e=>{a("sortChange",e)};function m(e){e&&(console.log("row :>> ",e),u.value.toggleRowSelection(e))}return(0,s.dl)((()=>{u?.value?.doLayout()})),(0,s.bv)((()=>{u.value.doLayout()})),t({tableRef:u,toggleRowSelection:m}),(t,a)=>{const p=n.$Y,m=i.GT,y=n.eI,h=l.R,w=o.t;return(0,s.wg)(),(0,s.iD)(s.HY,null,[(0,s.wy)(((0,s.wg)(),(0,s.j4)(y,{ref_key:"tableRef",ref:u,class:(0,r.C_)(["custom-table",e.className]),data:e.listData,border:!0,"row-key":e.idkey,"max-height":e.maxHeight,"span-method":e.spanMethod,stripe:e.stripe,onSelectionChange:c,onSortChange:f},{empty:(0,s.w5)((()=>[(0,s.Wm)(m,{description:"暂无数据"})])),default:(0,s.w5)((()=>[e.isSelection?((0,s.wg)(),(0,s.j4)(p,{key:0,fixed:"left",type:"selection",width:"55"})):(0,s.kq)("",!0),e.isIndex?((0,s.wg)(),(0,s.j4)(p,{key:1,fixed:"left",label:"序号",type:"index",width:"55"})):(0,s.kq)("",!0),((0,s.wg)(!0),(0,s.iD)(s.HY,null,(0,s.Ko)(e.columnList,(e=>((0,s.wg)(),(0,s.j4)(p,{key:e.prop,label:e.label,prop:e.prop,"show-overflow-tooltip":e.tooltip,"class-name":"operation"===e.prop?"operation-btns "+e.className:e.className,align:e.align?e.align:"left",sortable:e.sortable,width:e.width,"min-width":e.minWidth,fixed:"true"==e.fixed||"right"==e.fixed&&"right"},(0,s.Nv)({_:2},[e.isHeaderRender?{name:"header",fn:(0,s.w5)((()=>[(0,s.WI)(t.$slots,e.prop+"Header",{column:e})])),key:"0"}:void 0,e.isRender?{name:"default",fn:(0,s.w5)((({row:a,$index:o,column:l})=>[(0,s.WI)(t.$slots,e.prop,{row:a,index:o,column:l})])),key:"1"}:{name:"default",fn:(0,s.w5)((({row:t})=>[(0,s._)("span",{class:"name"},(0,r.zw)(t[e.prop]||"---"),1)])),key:"2"}]),1032,["label","prop","show-overflow-tooltip","class-name","align","sortable","width","min-width","fixed"])))),128))])),_:3},8,["class","data","row-key","max-height","span-method","stripe"])),[[w,e.listLoading]]),e.isPagination?((0,s.wg)(),(0,s.j4)(h,{key:0,"current-page":e.pageNo,layout:e.layout,"page-size":e.pageSize,total:e.total,onCurrentChange:g,onSizeChange:d},null,8,["current-page","layout","page-size","total"])):(0,s.kq)("",!0)],64)}}};function d(e){e.__source="src/components/CustomTable.vue"}var g=a(83744);"function"===typeof d&&d(u);const c=(0,g.Z)(u,[["__scopeId","data-v-bdd84b16"]]);var f=c}}]);