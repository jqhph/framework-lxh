/**
 * 初始化js
 *
 * Created by Jqh on 2017/7/3.
 */
/*! Sea.js 2.2.3 | seajs.org/LICENSE.md */
!function(a,b){function c(a){return function(b){return{}.toString.call(b)=="[object "+a+"]"}}function d(){return B++}function e(a){return a.match(E)[0]}function f(a){for(a=a.replace(F,"/");a.match(G);)a=a.replace(G,"/");return a=a.replace(H,"$1/")}function g(a){var b=a.length-1,c=a.charAt(b);return"#"===c?a.substring(0,b):".js"===a.substring(b-2)||a.indexOf("?")>0||".css"===a.substring(b-3)||"/"===c?a:a+".js"}function h(a){var b=v.alias;return b&&x(b[a])?b[a]:a}function i(a){var b=v.paths,c;return b&&(c=a.match(I))&&x(b[c[1]])&&(a=b[c[1]]+c[2]),a}function j(a){var b=v.vars;return b&&a.indexOf("{")>-1&&(a=a.replace(J,function(a,c){return x(b[c])?b[c]:a})),a}function k(a){var b=v.map,c=a;if(b)for(var d=0,e=b.length;e>d;d++){var f=b[d];if(c=z(f)?f(a)||a:a.replace(f[0],f[1]),c!==a)break}return c}function l(a,b){var c,d=a.charAt(0);if(K.test(a))c=a;else if("."===d)c=f((b?e(b):v.cwd)+a);else if("/"===d){var g=v.cwd.match(L);c=g?g[0]+a.substring(1):a}else c=v.base+a;return 0===c.indexOf("//")&&(c=location.protocol+c),c}function m(a,b){if(!a)return"";a=h(a),a=i(a),a=j(a),a=g(a);var c=l(a,b);return c=k(c)}function n(a){return a.hasAttribute?a.src:a.getAttribute("src",4)}function o(a,b,c,d){var e=T.test(a),f=M.createElement(e?"link":"script");c&&(f.charset=c),A(d)||f.setAttribute("crossorigin",d),p(f,b,e,a),e?(f.rel="stylesheet",f.href=a):(f.async=!0,f.src=a),U=f,S?R.insertBefore(f,S):R.appendChild(f),U=null}function p(a,c,d,e){function f(){a.onload=a.onerror=a.onreadystatechange=null,d||v.debug||R.removeChild(a),a=null,c()}var g="onload"in a;return!d||!W&&g?(g?(a.onload=f,a.onerror=function(){D("error",{uri:e,node:a}),f()}):a.onreadystatechange=function(){/loaded|complete/.test(a.readyState)&&f()},b):(setTimeout(function(){q(a,c)},1),b)}function q(a,b){var c=a.sheet,d;if(W)c&&(d=!0);else if(c)try{c.cssRules&&(d=!0)}catch(e){"NS_ERROR_DOM_SECURITY_ERR"===e.name&&(d=!0)}setTimeout(function(){d?b():q(a,b)},20)}function r(){if(U)return U;if(V&&"interactive"===V.readyState)return V;for(var a=R.getElementsByTagName("script"),b=a.length-1;b>=0;b--){var c=a[b];if("interactive"===c.readyState)return V=c}}function s(a){var b=[];return a.replace(Y,"").replace(X,function(a,c,d){d&&b.push(d)}),b}function t(a,b){this.uri=a,this.dependencies=b||[],this.exports=null,this.status=0,this._waitings={},this._remain=0}if(!a.seajs){var u=a.seajs={version:"2.2.3"},v=u.data={},w=c("Object"),x=c("String"),y=Array.isArray||c("Array"),z=c("Function"),A=c("Undefined"),B=0,C=v.events={};u.on=function(a,b){var c=C[a]||(C[a]=[]);return c.push(b),u},u.off=function(a,b){if(!a&&!b)return C=v.events={},u;var c=C[a];if(c)if(b)for(var d=c.length-1;d>=0;d--)c[d]===b&&c.splice(d,1);else delete C[a];return u};var D=u.emit=function(a,b){var c=C[a],d;if(c)for(c=c.slice();d=c.shift();)d(b);return u},E=/[^?#]*\//,F=/\/\.\//g,G=/\/[^/]+\/\.\.\//,H=/([^:/])\/\//g,I=/^([^/:]+)(\/.+)$/,J=/{([^{]+)}/g,K=/^\/\/.|:\//,L=/^.*?\/\/.*?\//,M=document,N=e(M.URL),O=M.scripts,P=M.getElementById("seajsnode")||O[O.length-1],Q=e(n(P)||N);u.resolve=m;var R=M.head||M.getElementsByTagName("head")[0]||M.documentElement,S=R.getElementsByTagName("base")[0],T=/\.css(?:\?|$)/i,U,V,W=+navigator.userAgent.replace(/.*(?:AppleWebKit|AndroidWebKit)\/(\d+).*/,"$1")<536;u.request=o;var X=/"(?:\\"|[^"])*"|'(?:\\'|[^'])*'|\/\*[\S\s]*?\*\/|\/(?:\\\/|[^\/\r\n])+\/(?=[^\/])|\/\/.*|\.\s*require|(?:^|[^$])\brequire\s*\(\s*(["'])(.+?)\1\s*\)/g,Y=/\\\\/g,Z=u.cache={},$,_={},ab={},bb={},cb=t.STATUS={FETCHING:1,SAVED:2,LOADING:3,LOADED:4,EXECUTING:5,EXECUTED:6};t.prototype.resolve=function(){for(var a=this,b=a.dependencies,c=[],d=0,e=b.length;e>d;d++)c[d]=t.resolve(b[d],a.uri);return c},t.prototype.load=function(){var a=this;if(!(a.status>=cb.LOADING)){a.status=cb.LOADING;var c=a.resolve();D("load",c);for(var d=a._remain=c.length,e,f=0;d>f;f++)e=t.get(c[f]),e.status<cb.LOADED?e._waitings[a.uri]=(e._waitings[a.uri]||0)+1:a._remain--;if(0===a._remain)return a.onload(),b;var g={};for(f=0;d>f;f++)e=Z[c[f]],e.status<cb.FETCHING?e.fetch(g):e.status===cb.SAVED&&e.load();for(var h in g)g.hasOwnProperty(h)&&g[h]()}},t.prototype.onload=function(){var a=this;a.status=cb.LOADED,a.callback&&a.callback();var b=a._waitings,c,d;for(c in b)b.hasOwnProperty(c)&&(d=Z[c],d._remain-=b[c],0===d._remain&&d.onload());delete a._waitings,delete a._remain},t.prototype.fetch=function(a){function c(){u.request(g.requestUri,g.onRequest,g.charset,g.crossorigin)}function d(){delete _[h],ab[h]=!0,$&&(t.save(f,$),$=null);var a,b=bb[h];for(delete bb[h];a=b.shift();)a.load()}var e=this,f=e.uri;e.status=cb.FETCHING;var g={uri:f};D("fetch",g);var h=g.requestUri||f;return!h||ab[h]?(e.load(),b):_[h]?(bb[h].push(e),b):(_[h]=!0,bb[h]=[e],D("request",g={uri:f,requestUri:h,onRequest:d,charset:z(v.charset)?v.charset(h):v.charset,crossorigin:z(v.crossorigin)?v.crossorigin(h):v.crossorigin}),g.requested||(a?a[g.requestUri]=c:c()),b)},t.prototype.exec=function(){function a(b){return t.get(a.resolve(b)).exec()}var c=this;if(c.status>=cb.EXECUTING)return c.exports;c.status=cb.EXECUTING;var e=c.uri;a.resolve=function(a){return t.resolve(a,e)},a.async=function(b,c){return t.use(b,c,e+"_async_"+d()),a};var f=c.factory,g=z(f)?f(a,c.exports={},c):f;return g===b&&(g=c.exports),delete c.factory,c.exports=g,c.status=cb.EXECUTED,D("exec",c),g},t.resolve=function(a,b){var c={id:a,refUri:b};return D("resolve",c),c.uri||u.resolve(c.id,b)},t.define=function(a,c,d){var e=arguments.length;1===e?(d=a,a=b):2===e&&(d=c,y(a)?(c=a,a=b):c=b),!y(c)&&z(d)&&(c=s(""+d));var f={id:a,uri:t.resolve(a),deps:c,factory:d};if(!f.uri&&M.attachEvent){var g=r();g&&(f.uri=g.src)}D("define",f),f.uri?t.save(f.uri,f):$=f},t.save=function(a,b){var c=t.get(a);c.status<cb.SAVED&&(c.id=b.id||a,c.dependencies=b.deps||[],c.factory=b.factory,c.status=cb.SAVED)},t.get=function(a,b){return Z[a]||(Z[a]=new t(a,b))},t.use=function(b,c,d){var e=t.get(d,y(b)?b:[b]);e.callback=function(){for(var b=[],d=e.resolve(),f=0,g=d.length;g>f;f++)b[f]=Z[d[f]].exec();c&&c.apply(a,b),delete e.callback},e.load()},t.preload=function(a){var b=v.preload,c=b.length;c?t.use(b,function(){b.splice(0,c),t.preload(a)},v.cwd+"_preload_"+d()):a()},u.use=function(a,b){return t.preload(function(){t.use(a,b,v.cwd+"_use_"+d())}),u},t.define.cmd={},a.define=t.define,u.Module=t,v.fetchedList=ab,v.cid=d,u.require=function(a){var b=t.get(t.resolve(a));return b.status<cb.EXECUTING&&(b.onload(),b.exec()),b.exports};var db=/^(.+?\/)(\?\?)?(seajs\/)+/;v.base=(Q.match(db)||["",Q])[1],v.dir=Q,v.cwd=N,v.charset="utf-8",v.preload=function(){var a=[],b=location.search.replace(/(seajs-\w+)(&|$)/g,"$1=1$2");return b+=" "+M.cookie,b.replace(/(seajs-\w+)=1/g,function(b,c){a.push(c)}),a}(),u.config=function(a){for(var b in a){var c=a[b],d=v[b];if(d&&w(d))for(var e in c)d[e]=c[e];else y(d)?c=d.concat(c):"base"===b&&("/"!==c.slice(-1)&&(c+="/"),c=l(c)),v[b]=c}return D("config",a),u}}}(this);
!function(){function a(a){return function(b){return{}.toString.call(b)=="[object "+a+"]"}}function b(a){return"[object Function]"=={}.toString.call(a)}function c(a,c,e,f){var g=u.test(a),h=r.createElement(g?"link":"script");if(e){var i=b(e)?e(a):e;i&&(h.charset=i)}void 0!==f&&h.setAttribute("crossorigin",f),d(h,c,g,a),g?(h.rel="stylesheet",h.href=a):(h.async=!0,h.src=a),p=h,t?s.insertBefore(h,t):s.appendChild(h),p=null}function d(a,b,c,d){function f(){a.onload=a.onerror=a.onreadystatechange=null,c||seajs.data.debug||s.removeChild(a),a=null,b()}var g="onload"in a;return!c||!v&&g?(g?(a.onload=f,a.onerror=function(){seajs.emit("error",{uri:d,node:a}),f()}):a.onreadystatechange=function(){/loaded|complete/.test(a.readyState)&&f()},void 0):(setTimeout(function(){e(a,b)},1),void 0)}function e(a,b){var c,d=a.sheet;if(v)d&&(c=!0);else if(d)try{d.cssRules&&(c=!0)}catch(f){"NS_ERROR_DOM_SECURITY_ERR"===f.name&&(c=!0)}setTimeout(function(){c?b():e(a,b)},20)}function f(a){return a.match(x)[0]}function g(a){for(a=a.replace(y,"/"),a=a.replace(A,"$1/");a.match(z);)a=a.replace(z,"/");return a}function h(a){var b=a.length-1,c=a.charAt(b);return"#"===c?a.substring(0,b):".js"===a.substring(b-2)||a.indexOf("?")>0||".css"===a.substring(b-3)||"/"===c?a:a+".js"}function i(a){var b=w.alias;return b&&q(b[a])?b[a]:a}function j(a){var b,c=w.paths;return c&&(b=a.match(B))&&q(c[b[1]])&&(a=c[b[1]]+b[2]),a}function k(a){var b=w.vars;return b&&a.indexOf("{")>-1&&(a=a.replace(C,function(a,c){return q(b[c])?b[c]:a})),a}function l(a){var c=w.map,d=a;if(c)for(var e=0,f=c.length;f>e;e++){var g=c[e];if(d=b(g)?g(a)||a:a.replace(g[0],g[1]),d!==a)break}return d}function m(a,b){var c,d=a.charAt(0);if(D.test(a))c=a;else if("."===d)c=g((b?f(b):w.cwd)+a);else if("/"===d){var e=w.cwd.match(E);c=e?e[0]+a.substring(1):a}else c=w.base+a;return 0===c.indexOf("//")&&(c=location.protocol+c),c}function n(a,b){if(!a)return"";a=i(a),a=j(a),a=k(a),a=h(a);var c=m(a,b);return c=l(c)}function o(a){return a.hasAttribute?a.src:a.getAttribute("src",4)}var p,q=a("String"),r=document,s=r.head||r.getElementsByTagName("head")[0]||r.documentElement,t=s.getElementsByTagName("base")[0],u=/\.css(?:\?|$)/i,v=+navigator.userAgent.replace(/.*(?:AppleWebKit|AndroidWebKit)\/?(\d+).*/i,"$1")<536;seajs.request=c;var w=seajs.data,x=/[^?#]*\//,y=/\/\.\//g,z=/\/[^/]+\/\.\.\//,A=/([^:/])\/+\//g,B=/^([^/:]+)(\/.+)$/,C=/{([^{]+)}/g,D=/^\/\/.|:\//,E=/^.*?\/\/.*?\//,r=document,F=location.href&&0!==location.href.indexOf("about:")?f(location.href):"",G=r.scripts,H=r.getElementById("seajsnode")||G[G.length-1];f(o(H)||F),seajs.resolve=n,define("seajs/seajs-css/1.0.5/seajs-css",[],{})}();
// blade js
window.BladeConfig={version:"1.0-dev",customTags:{},addTag:function(a,b){if(typeof b!="function"){throw new Error("Invalid argument")}this.customTags[a]=b}};window.BladeConfig={version:"1.0-dev",customTags:{},addTag:function(a,b){if(typeof b!="function"){throw new Error("Invalid argument")}this.customTags[a]=b}};window.Blade=function(tpl,vars){var store={selector:"#blade",call:null,tpl:tpl,vars:{},delimiter:{start:"{",end:"}"},placeholders:{$if:"@if",$else:"@else",$elseif:"@elseif",$endif:"@endif",$foreach:"@foreach",$endforeach:"@endforeach",$end:"@",$tags:{compare:"#compare"},$customTags:{}},regs:{$var:/{([a-z|_]*[0-9]*[_]*[\.]*([a-z|_]*[0-9]*[_]*)+([\[]?([a-z|_]*[0-9]*[_]*[\.]*([a-z|_]*[0-9]*[_]*)+)[\]]?))}/gi,stringVar:/[\[]([a-z|_]*[0-9]*[_]*[\.]*([a-z|_]*[0-9]*[_]*)+)[\]]/gi,jsvar:/[ ]([\'|\"]*[\[_\.\]a-z]+[0-9\]]*[\'|\"]*)+/gi,compareJsvar:/[ ]+((?![\'|\"])[\[_\.\]a-z]+[0-9\]]*)+[ ]*/gi,$if:/@if[ ]+([\s\S.])+@endif\b/gi,$foreach:/@foreach[ ]+([\s\S.])+@endforeach\b/gi,tag:/{(#[^{|{#]+)}/gi,tagName:/#[a-z]*\b/i,customTag:/{(#[^{|{#]+)#}/gi,"@if":/@if\b/gi,"@endif":/@endif\b/gi,"@elseif":/@elseif\b/gi,"@else":/@else\b/gi,"@foreach":/@foreach\b/gi,"@endforeach":/@endforeach\b/gi,},model:{$if:"\n if ({exp}) { \n {content} \n } \n",$elseif:" else if ({exp}) { \n {content} \n } \n",$else:" else { \n {content} \n } \n"}};store.tpl=tpl;store.vars=vars||{};store.placeholders.$customTags=BladeConfig.customTags;var self=this;this.getVars=function(){return store.vars};this.getTpl=function(){return store.tpl};this.fetch=function(vars){store.vars=vars||store.vars;syntax_analysis(store.tpl);return parse_var(parse_custom_tag(parse_tag(parse_expression_if(parse_expression_foreach(store.tpl)))))};this.render=function(selector,callback){store.selector=selector||store.selector;store.call=callback||store.call;document.querySelector(store.selector).innerHTML=this.fetch();typeof store.call!="function"||store.call(this)};this.rerender=function(vars){store.vars=vars||store.vars;this.render()};this.assign=function(key,value){if(typeof key=="object"){store.vars=key}else{store.vars[key]=value}};var parse_custom_tag=function(tpl){return tpl.replace(store.regs.customTag,function(full,$match,position){var tagName=$match.match(store.regs.tagName),d,i,j,o,t,q;$match=$match.replace(tagName[0],"");tagName=tagName[0].replace("#","");for(i in store.placeholders.$customTags){if(i!=tagName){continue}d=trim(compile_tags.transVar($match,false,"!!"));d=d.split("!!");t=[];t.push(self);for(j in d){if(typeof d[j]=="function"){continue}if(is_object(d[j])){d[j]=JSON.parse(trim(d[j]));t.push(d[j])}else{d[j]=d[j].replace(/'|"/g,"");if(typeof d[j]!="function"){t.push(trim(d[j]))}}}return store.placeholders.$customTags[i].apply(self,t)}return full})};var is_object=function(d){if((d.indexOf("{")===0&&d.indexOf("}")!=-1)||(d.indexOf("[")===0&&d.indexOf("]")!=-1)){return true}return false};var parse_tag=function(tpl){if(!tpl){return""}return tpl.replace(store.regs.tag,function(full,$match,position){var tagName=$match.match(store.regs.tagName);tagName=tagName[0];for(var i in store.placeholders.$tags){if(tagName==store.placeholders.$tags[i]){return compile_tags[i]($match.replace(tagName,""))}}return full})};var compile_tags={compare:function(tpl){var tagContent=this.compareSyntaxAnalysis(tpl,"??",2);var content=this.compareSyntaxAnalysis(tagContent[1],"::",1);var exp="{";for(var i in content){if(typeof content[i]=="function"){continue}if(i==1){exp+="} else {"}if(content[i].indexOf("'")!=-1||content[i].indexOf('"')!=-1){exp+=" var result = "+content[i]}else{exp+=" var result = "+this.transVar(content[i],true)}}exp="if ("+this.compareTransVar(tagContent[0],true)+") "+exp+"}";eval(exp);return result||""},transVar:function(tpl,toString,delimiter){delimiter=delimiter||" ";var i=0,d,s=delimiter,e=delimiter;return tpl.replace(store.regs.jsvar,function(full,$match){d=trans_var($match,null,toString);i++;if(i==1){s=""}return(typeof d=="object"?s+JSON.stringify(d)+e:s+d+e)})},compareTransVar:function(tpl,toString,delimiter){delimiter=delimiter||" ";return tpl.replace(store.regs.compareJsvar,function(full,$match){var d=trans_var($match,null,toString);return(typeof d=="object"?delimiter+JSON.stringify(d)+delimiter:delimiter+d+delimiter)})},compareSyntaxAnalysis:function(content,type,length){content=content.split(type);if(content.length<length){throw new Error('Syntax error: "'+store.placeholders.$tags.compare+'" expression is not legal')}return content}};var parse_expression_foreach=function(tpl){return tpl.replace(store.regs.$foreach,function(full,$match,position){var results=get_exp_and_content("foreach","endforeach",full,true,true);var tmpExps=results.exp.split(" "),exps=[];for(var i=0;i<tmpExps.length;i++){if(!tmpExps[i]||tmpExps[i]=="\n"){continue}exps.push(tmpExps[i].replace(/[\s\n]/gi,""))}var content="",list=[],key=null,value,i,foreachTpl,hasSubForeach=false;list=exps.shift();list=get_var_name(list);list=get_var(list)||[];if(exps.length>1){key=exps.shift()}value=exps.pop();for(i in list){if(typeof list[i]=="function"){continue}foreachTpl=results.content;if(key){self.assign(get_var_name(key),i)}self.assign(get_var_name(value),list[i]);if(hasSubForeach||has("foreach",foreachTpl)){hasSubForeach=true;foreachTpl=parse_expression_foreach(foreachTpl)}content+=parse_var(parse_custom_tag(parse_tag(parse_expression_if(foreachTpl))))}return content})};var parse_expression_if=function(tpl,notParseForeach){return tpl.replace(store.regs.$if,function(full,$match,position){var ifContent=get_exp_and_content("if","end",full);full=ifContent.full;var allContents=[];allContents.push(ifContent.content);var evalString=get_eval_string("if",ifContent,0);var elseifTimes=get_length("elseif",full);for(var i=1;i<=elseifTimes;i++){var tmp=get_exp_and_content("elseif","end",full);full=tmp.full;allContents.push(tmp.content);evalString+=get_eval_string("elseif",tmp,i)}var elseTimes=get_length("else",full);if(elseTimes){tmp=get_exp_and_content("else","end",full);if(tmp.full){full=tmp.full.replace("else","")}allContents.push(tmp.content);evalString+=get_eval_string("else",tmp,i)}eval(evalString);if(full){full=full.replace(store.placeholders.$endif,"")}return(parse_var(parse_custom_tag(parse_tag(allContents[key])))||"")+full})};var parse_var=function(tpl,vars,toString,$default){if(!tpl){return null}return tpl.replace(store.regs.$var,function(full,$match,position){return trans_var($match,vars,toString,$default)})};var trans_var=function($var,vars,toString,$default){if($var.indexOf("[")==-1){if(toString){var t=get_var($var,vars,$default);if(typeof t=="object"){return JSON.stringify(t)}else{if(typeof t=="boolean"){return t}}return'"'+t+'"'}return get_var($var,vars,$default)}else{return get_var($var.replace(store.regs.stringVar,function(f,$m){return"."+get_var($m,vars,$default)}),$default)}};var get_var=function($key,vars,$default){var $lastItem=vars||store.vars,keys=$key.split(".");for(var i=0;i<keys.length;i++){if(typeof $lastItem[keys[i]]!="undefined"){$lastItem=$lastItem[keys[i]]}else{return(typeof $default!="undefined")?$default:$key}}return $lastItem};function syntax_analysis(tpl){var msg="Syntax error: ";var ifTimes=get_length("if",tpl),endifTimes=get_length("endif",tpl);if(ifTimes>endifTimes){throw new Error(msg+'miss "'+store.placeholders.$endif+'"')}else{if(ifTimes<endifTimes){throw new Error(msg+'miss "'+store.placeholders.$if+'"')}}var elseTimes=get_length("else",tpl);if(elseTimes>ifTimes){throw new Error(msg+'redundant "'+store.placeholders.$else+'" placeholder')}var elseifTimes=get_length("elseif",tpl);if(ifTimes==0&&elseifTimes>0){throw new Error(msg+'redundant "'+store.placeholders.$elseif+'" placeholder')}var foreachTimes=get_length("foreach",tpl);var endforeachTimes=get_length("endforeach",tpl);if(foreachTimes>endforeachTimes){throw new Error(msg+'miss "'+store.placeholders.$endforeach+'"')}else{if(foreachTimes<endforeachTimes){throw new Error(msg+'miss "'+store.placeholders.$foreach+'"')}}}function get_var_name($key){return $key.replace(new RegExp("["+store.delimiter.start+"|"+store.delimiter.end+"]","gi"),"")}function get_eval_string(type,tmp,i){var exp=parse_var(tmp.exp,null,true,"");var str=store.model["$"+type].replace("{exp}",exp);return str.replace("{content}",'var key = "'+i+'"')}function has(type,content){if(content.indexOf(store.placeholders["$"+type])!=-1&&content.indexOf(store.placeholders["$end"+type])!=-1){return true}return false}function get_exp_and_content(startType,endType,values,getAll,unset){var start=store.placeholders["$"+startType],end=store.placeholders["$"+endType];var exp=values.match(new RegExp(start+"[ ]*([^@]*?)\n","i"));if(!exp||!exp[0]){throw new Error('Syntax error: "'+start+'" miss expression content')}values=values.replace(exp[0],"");if(unset){if(end!="@"){var index=values.lastIndexOf(end);if(index!=-1){values=values.substr(0,index)}else{values=values.replace(end,"")}}}exp=exp[0].replace(new RegExp("(?:"+start+"[ ])*|\n","gi"),"");if(has(startType,values)){if(startType=="if"){values=parse_expression_if(values)}}var content="";if(getAll){content=values}else{content=values.match(new RegExp("([^"+end+"]*)","gi"));content=content[0]}values=values.replace(content,"");return{exp:exp,content:content,full:values}}function get_length(type,tpl){var t=tpl.match(store.regs[store.placeholders["$"+type]]);if(!t){return 0}return t.length}function trim(tpl){return tpl.replace(/(^\s*)|(\s*$)/g,"")}};

(function (window) {
    var config = get_config(), $cache = new Cache();

    dispatcher();

    function dispatcher() {
        // 设置缓存token
        $cache.setToken(config.options.config['js-version']);

        config.options.cache = $cache;
        // 处理需要加载的js数组
        config.publicJs = get_public_js(config.publicJs, config.options.config['js-version']);
        // 处理需要加载的js数组
        config.publicCss = get_public_css(config.publicCss, config.options.config['js-version']);

        config.seaConfig = get_sea_config(config.seaConfig, config.options.config['js-version']);

        seajs.config(config.seaConfig);
        // 加载css
        seajs.use(config.publicCss);

        // 优先加载jquery
        // seajs.use('jquery', function (q) {
        seajs.use(config.publicJs, function () {
            var plugIns = arguments; // 所有加载进来的js插件变量数组
            init(function () {
                $(function () {
                    call_actions(plugIns);
                })
            })

        })
    }

    // 初始化完成，执行动作
    function call_actions(plugIns) {
        for (var i in lxhActions) {
            if (typeof lxhActions[i] == 'function') {
                lxhActions[i].apply(this, plugIns);
            }
        }
    }

    /**
     * 初始化
     *
     * @param call
     */
    function init(call) {
        window.$lxh = new Lxh(config.options);

        var lang = $lxh.config().get('language');

        var serverOptions = $lxh.createStore({});
        if (typeof load_data == 'function') {
            serverOptions.set(load_data());
        }

        // 语言包设置
        $lxh.language().type(lang);
        // 注入语言包数据
        $lxh.language().fill(serverOptions.get('language') || null, true);

        // 生成table 展示隐藏字段功能按键
        $('[data-pattern]').each(function () {
            var $tableScrollWrapper = $(this);
            if (typeof $tableScrollWrapper.responsiveTable != 'undefined') {
                $tableScrollWrapper.responsiveTable($tableScrollWrapper.data());
            }
        });
        
        call()
    }

    function get_lang_cache_key(lang) {
        return 'language_' + lang
    }

    /**
     * 检测缓存中是否存在语言包，返回需要加载的语言包模块
     *
     * @param lang
     * @param scopes
     * @param useCache
     * @returns {*}
     */
    function check_cache_language(lang, scopes, useCache) {
        var cacheKey = get_lang_cache_key(lang), package = $cache.get(cacheKey), t = [], i;

        if (typeof add_lang_scopes == 'function') {
            var addScopes = add_lang_scopes();
            for (i in addScopes) {
                scopes.push(addScopes[i]);
            }
        }

        if (! package || ! useCache) return scopes || [];
        for (i in scopes) {
            if (! package[scopes[i]]) t.push(scopes[i]);
        }
        return t || [];
    }

    /**
     * 处理需要加载的css数组
     *
     * @param publicCss
     * @param v
     * @returns {*}
     */
    function get_public_css(publicCss, v) {
        if (typeof cssLibArr != 'undefined') {
            cssLibArr = array_unique(cssLibArr);
            for (var i in cssLibArr) {
                publicCss.push(cssLibArr[i]);
            }
        }

        for (i in publicCss) {
            publicCss[i] = publicCss[i] + '?v=' + v;
        }
        return publicCss;
    }

    /**
     * 处理需要加载的js数组
     *
     * @param publicJs
     * @param version
     * @returns {*}
     */
    function get_public_js(publicJs, version) {
        if (typeof jsLibArr != 'undefined') {
            jsLibArr = array_unique(jsLibArr);
            for (var i in jsLibArr) {
                publicJs.push(jsLibArr[i] + '.js?v=' + version);
            }
        }

        var scopes = check_cache_language(config.options.config.language, config.langScopes, config.options.config['use-cache']);
        var loads = {};

        // 判断是否需要载入语言包
        if (scopes.length > 0) {
            // publicJs.push('api/language?scopes=' + scopes.join(',') + '&lang=' + config.options.config.language)
            loads.language = scopes.join(',');
        }

        var jsApi = get_load_data_js_api(loads);

        if (jsApi) {
            publicJs.unshift(jsApi);
        }

        return publicJs;
        
        function get_load_data_js_api(data) {
            var api = config.options.dataApi;

            var p = '';
            for (var i in data) {
                p += '&n[]=' + i + ':' + data[i];
            }

            if (p) {
                return api + '?' + p;
            }
            return ''
        }
    }

    /**
     * 处理seajs配置
     *
     * @param config
     * @param version
     * @returns {*}
     */
    function get_sea_config(config, version) {
        for (var i in config.alias) {
            config.alias[i] = config.alias[i] + '.js?v=' + version;
        }
        return config;
    }

    /**
     * 缓存管理类
     *
     * @constructor
     */
    function Cache() {
        this.storage = window.localStorage || {};

        /**
         * token值，用于跟服务器的token进行对比，如两值不同则刷新缓存
         *
         * @type {null|int|string}
         */
        this.token = null;

        /**
         * 缓存前缀
         *
         * @type {{general: string, timeout: string}}
         */
        this.prefix = {
            general: "$lxh_",
            timeout: "@lxh_"
        };

        /**
         * 设置token
         *
         * @param token
         */
        this.setToken = function (token) {
            this.token = token
        };

        /**
         * 缓存token
         *
         * @param token
         */
        this.saveToken = function (token) {
            this.set('$$token', token || this.token);
        };

        /**
         * 设置缓存
         *
         * @param key
         * @param val
         */
        this.set = function (key, val) {
            if (val instanceof Object) {
                val = JSON.stringify(val);
            }
            this.storage.setItem(this.prefix.general + key, val);
        };

        /**
         * 获取缓存
         *
         * @param key
         * @param def
         * @returns {*}
         */
        this.get = function (key, def) {
            if (! this.checkTokenValid(key)) {
                return def || null;
            }
            //检测是否过期
            if (this.clearTimeout(key)) return null;
            var val = this.storage.getItem(this.prefix.general + key);

            if (val) {
                if (val.indexOf("{") === 0 || val.indexOf("[") === 0) {
                    return JSON.parse(val);
                }
                return val;
            }
            return (def || null);
        };

        /**
         * 检查是否应该更新缓存，是则返回false，否则返回true
         *
         * @param key
         * @returns {boolean}
         */
        this.checkTokenValid = function (key) {
            if (key == '$$token') {
                return true;
            }
            if (this.token != this.get('$$token')) {
                this.clearAll();
                this.saveToken();
                return false;
            }
            return true;
        };

        /**
         * 清除所有过期的key
         *
         */
        this.clearPastDueKey = function () {
            for (var key in this.storage) {
                if (key.indexOf(this.prefix.timeout) == -1) {
                    continue;
                }
                this.clearTimeout(key.replace(this.prefix.timeout, ""));
            }
        };

        /**
         * 检查key是否过期，是则清除并返回true，否则返回false
         *
         * @param key
         * @returns {boolean}
         */
        this.clearTimeout = function (key) {
            var d, timeoutKey = this.prefix.timeout + key, timeout = this.storage.getItem(timeoutKey);

            if (timeout) {
                d = new Date().getTime();
                if (timeout < d) {//已过期
                    delete this.storage[this.prefix.general + key];
                    delete this.storage[timeoutKey];
                    return true;
                }
            }
            return false
        };

        /**
         * 设置缓存时间，tiemeout毫秒后过期
         *
         * @param key
         * @param timeout
         */
        this.expire = function (key, timeout) {
            var d = new Date().getTime() + (parseInt(timeout));
            this.storage.setItem(this.prefix.timeout + key, d);
        };

        /**
         * 具体某一时间点过期
         *
         * @param key
         * @param timeout
         */
        this.expireAt = function (key, timeout) {
            this.storage.setItem(this.prefix.timeout + key, timeout);
        };

        /**
         * 清除所有缓存
         *
         */
        this.clearAll = function () {
            for (var i in this.storage) {
                delete this.storage[i];
            }
        };

        this.clearPastDueKey()
    }
})(window);
