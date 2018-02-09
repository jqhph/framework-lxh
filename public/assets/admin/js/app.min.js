/**
 * 初始化js
 *
 * Created by Jqh on 2017/7/3.
 */
/*! Sea.js 2.2.3 | seajs.org/LICENSE.md */
!function(a,b){function c(a){return function(b){return{}.toString.call(b)=="[object "+a+"]"}}function d(){return B++}function e(a){return a.match(E)[0]}function f(a){for(a=a.replace(F,"/");a.match(G);)a=a.replace(G,"/");return a=a.replace(H,"$1/")}function g(a){var b=a.length-1,c=a.charAt(b);return"#"===c?a.substring(0,b):".js"===a.substring(b-2)||a.indexOf("?")>0||".css"===a.substring(b-3)||"/"===c?a:a+".js"}function h(a){var b=v.alias;return b&&x(b[a])?b[a]:a}function i(a){var b=v.paths,c;return b&&(c=a.match(I))&&x(b[c[1]])&&(a=b[c[1]]+c[2]),a}function j(a){var b=v.vars;return b&&a.indexOf("{")>-1&&(a=a.replace(J,function(a,c){return x(b[c])?b[c]:a})),a}function k(a){var b=v.map,c=a;if(b)for(var d=0,e=b.length;e>d;d++){var f=b[d];if(c=z(f)?f(a)||a:a.replace(f[0],f[1]),c!==a)break}return c}function l(a,b){var c,d=a.charAt(0);if(K.test(a))c=a;else if("."===d)c=f((b?e(b):v.cwd)+a);else if("/"===d){var g=v.cwd.match(L);c=g?g[0]+a.substring(1):a}else c=v.base+a;return 0===c.indexOf("//")&&(c=location.protocol+c),c}function m(a,b){if(!a)return"";a=h(a),a=i(a),a=j(a),a=g(a);var c=l(a,b);return c=k(c)}function n(a){return a.hasAttribute?a.src:a.getAttribute("src",4)}function o(a,b,c,d){var e=T.test(a),f=M.createElement(e?"link":"script");c&&(f.charset=c),A(d)||f.setAttribute("crossorigin",d),p(f,b,e,a),e?(f.rel="stylesheet",f.href=a):(f.async=!0,f.src=a),U=f,S?R.insertBefore(f,S):R.appendChild(f),U=null}function p(a,c,d,e){function f(){a.onload=a.onerror=a.onreadystatechange=null,d||v.debug||R.removeChild(a),a=null,c()}var g="onload"in a;return!d||!W&&g?(g?(a.onload=f,a.onerror=function(){D("error",{uri:e,node:a}),f()}):a.onreadystatechange=function(){/loaded|complete/.test(a.readyState)&&f()},b):(setTimeout(function(){q(a,c)},1),b)}function q(a,b){var c=a.sheet,d;if(W)c&&(d=!0);else if(c)try{c.cssRules&&(d=!0)}catch(e){"NS_ERROR_DOM_SECURITY_ERR"===e.name&&(d=!0)}setTimeout(function(){d?b():q(a,b)},20)}function r(){if(U)return U;if(V&&"interactive"===V.readyState)return V;for(var a=R.getElementsByTagName("script"),b=a.length-1;b>=0;b--){var c=a[b];if("interactive"===c.readyState)return V=c}}function s(a){var b=[];return a.replace(Y,"").replace(X,function(a,c,d){d&&b.push(d)}),b}function t(a,b){this.uri=a,this.dependencies=b||[],this.exports=null,this.status=0,this._waitings={},this._remain=0}if(!a.seajs){var u=a.seajs={version:"2.2.3"},v=u.data={},w=c("Object"),x=c("String"),y=Array.isArray||c("Array"),z=c("Function"),A=c("Undefined"),B=0,C=v.events={};u.on=function(a,b){var c=C[a]||(C[a]=[]);return c.push(b),u},u.off=function(a,b){if(!a&&!b)return C=v.events={},u;var c=C[a];if(c)if(b)for(var d=c.length-1;d>=0;d--)c[d]===b&&c.splice(d,1);else delete C[a];return u};var D=u.emit=function(a,b){var c=C[a],d;if(c)for(c=c.slice();d=c.shift();)d(b);return u},E=/[^?#]*\//,F=/\/\.\//g,G=/\/[^/]+\/\.\.\//,H=/([^:/])\/\//g,I=/^([^/:]+)(\/.+)$/,J=/{([^{]+)}/g,K=/^\/\/.|:\//,L=/^.*?\/\/.*?\//,M=document,N=e(M.URL),O=M.scripts,P=M.getElementById("seajsnode")||O[O.length-1],Q=e(n(P)||N);u.resolve=m;var R=M.head||M.getElementsByTagName("head")[0]||M.documentElement,S=R.getElementsByTagName("base")[0],T=/\.css(?:\?|$)/i,U,V,W=+navigator.userAgent.replace(/.*(?:AppleWebKit|AndroidWebKit)\/(\d+).*/,"$1")<536;u.request=o;var X=/"(?:\\"|[^"])*"|'(?:\\'|[^'])*'|\/\*[\S\s]*?\*\/|\/(?:\\\/|[^\/\r\n])+\/(?=[^\/])|\/\/.*|\.\s*require|(?:^|[^$])\brequire\s*\(\s*(["'])(.+?)\1\s*\)/g,Y=/\\\\/g,Z=u.cache={},$,_={},ab={},bb={},cb=t.STATUS={FETCHING:1,SAVED:2,LOADING:3,LOADED:4,EXECUTING:5,EXECUTED:6};t.prototype.resolve=function(){for(var a=this,b=a.dependencies,c=[],d=0,e=b.length;e>d;d++)c[d]=t.resolve(b[d],a.uri);return c},t.prototype.load=function(){var a=this;if(!(a.status>=cb.LOADING)){a.status=cb.LOADING;var c=a.resolve();D("load",c);for(var d=a._remain=c.length,e,f=0;d>f;f++)e=t.get(c[f]),e.status<cb.LOADED?e._waitings[a.uri]=(e._waitings[a.uri]||0)+1:a._remain--;if(0===a._remain)return a.onload(),b;var g={};for(f=0;d>f;f++)e=Z[c[f]],e.status<cb.FETCHING?e.fetch(g):e.status===cb.SAVED&&e.load();for(var h in g)g.hasOwnProperty(h)&&g[h]()}},t.prototype.onload=function(){var a=this;a.status=cb.LOADED,a.callback&&a.callback();var b=a._waitings,c,d;for(c in b)b.hasOwnProperty(c)&&(d=Z[c],d._remain-=b[c],0===d._remain&&d.onload());delete a._waitings,delete a._remain},t.prototype.fetch=function(a){function c(){u.request(g.requestUri,g.onRequest,g.charset,g.crossorigin)}function d(){delete _[h],ab[h]=!0,$&&(t.save(f,$),$=null);var a,b=bb[h];for(delete bb[h];a=b.shift();)a.load()}var e=this,f=e.uri;e.status=cb.FETCHING;var g={uri:f};D("fetch",g);var h=g.requestUri||f;return!h||ab[h]?(e.load(),b):_[h]?(bb[h].push(e),b):(_[h]=!0,bb[h]=[e],D("request",g={uri:f,requestUri:h,onRequest:d,charset:z(v.charset)?v.charset(h):v.charset,crossorigin:z(v.crossorigin)?v.crossorigin(h):v.crossorigin}),g.requested||(a?a[g.requestUri]=c:c()),b)},t.prototype.exec=function(){function a(b){return t.get(a.resolve(b)).exec()}var c=this;if(c.status>=cb.EXECUTING)return c.exports;c.status=cb.EXECUTING;var e=c.uri;a.resolve=function(a){return t.resolve(a,e)},a.async=function(b,c){return t.use(b,c,e+"_async_"+d()),a};var f=c.factory,g=z(f)?f(a,c.exports={},c):f;return g===b&&(g=c.exports),delete c.factory,c.exports=g,c.status=cb.EXECUTED,D("exec",c),g},t.resolve=function(a,b){var c={id:a,refUri:b};return D("resolve",c),c.uri||u.resolve(c.id,b)},t.define=function(a,c,d){var e=arguments.length;1===e?(d=a,a=b):2===e&&(d=c,y(a)?(c=a,a=b):c=b),!y(c)&&z(d)&&(c=s(""+d));var f={id:a,uri:t.resolve(a),deps:c,factory:d};if(!f.uri&&M.attachEvent){var g=r();g&&(f.uri=g.src)}D("define",f),f.uri?t.save(f.uri,f):$=f},t.save=function(a,b){var c=t.get(a);c.status<cb.SAVED&&(c.id=b.id||a,c.dependencies=b.deps||[],c.factory=b.factory,c.status=cb.SAVED)},t.get=function(a,b){return Z[a]||(Z[a]=new t(a,b))},t.use=function(b,c,d){var e=t.get(d,y(b)?b:[b]);e.callback=function(){for(var b=[],d=e.resolve(),f=0,g=d.length;g>f;f++)b[f]=Z[d[f]].exec();c&&c.apply(a,b),delete e.callback},e.load()},t.preload=function(a){var b=v.preload,c=b.length;c?t.use(b,function(){b.splice(0,c),t.preload(a)},v.cwd+"_preload_"+d()):a()},u.use=function(a,b){return t.preload(function(){t.use(a,b,v.cwd+"_use_"+d())}),u},t.define.cmd={},a.define=t.define,u.Module=t,v.fetchedList=ab,v.cid=d,u.require=function(a){var b=t.get(t.resolve(a));return b.status<cb.EXECUTING&&(b.onload(),b.exec()),b.exports};var db=/^(.+?\/)(\?\?)?(seajs\/)+/;v.base=(Q.match(db)||["",Q])[1],v.dir=Q,v.cwd=N,v.charset="utf-8",v.preload=function(){var a=[],b=location.search.replace(/(seajs-\w+)(&|$)/g,"$1=1$2");return b+=" "+M.cookie,b.replace(/(seajs-\w+)=1/g,function(b,c){a.push(c)}),a}(),u.config=function(a){for(var b in a){var c=a[b],d=v[b];if(d&&w(d))for(var e in c)d[e]=c[e];else y(d)?c=d.concat(c):"base"===b&&("/"!==c.slice(-1)&&(c+="/"),c=l(c)),v[b]=c}return D("config",a),u}}}(this);
!function(){function a(a){return function(b){return{}.toString.call(b)=="[object "+a+"]"}}function b(a){return"[object Function]"=={}.toString.call(a)}function c(a,c,e,f){var g=u.test(a),h=r.createElement(g?"link":"script");if(e){var i=b(e)?e(a):e;i&&(h.charset=i)}void 0!==f&&h.setAttribute("crossorigin",f),d(h,c,g,a),g?(h.rel="stylesheet",h.href=a):(h.async=!0,h.src=a),p=h,t?s.insertBefore(h,t):s.appendChild(h),p=null}function d(a,b,c,d){function f(){a.onload=a.onerror=a.onreadystatechange=null,c||seajs.data.debug||s.removeChild(a),a=null,b()}var g="onload"in a;return!c||!v&&g?(g?(a.onload=f,a.onerror=function(){seajs.emit("error",{uri:d,node:a}),f()}):a.onreadystatechange=function(){/loaded|complete/.test(a.readyState)&&f()},void 0):(setTimeout(function(){e(a,b)},1),void 0)}function e(a,b){var c,d=a.sheet;if(v)d&&(c=!0);else if(d)try{d.cssRules&&(c=!0)}catch(f){"NS_ERROR_DOM_SECURITY_ERR"===f.name&&(c=!0)}setTimeout(function(){c?b():e(a,b)},20)}function f(a){return a.match(x)[0]}function g(a){for(a=a.replace(y,"/"),a=a.replace(A,"$1/");a.match(z);)a=a.replace(z,"/");return a}function h(a){var b=a.length-1,c=a.charAt(b);return"#"===c?a.substring(0,b):".js"===a.substring(b-2)||a.indexOf("?")>0||".css"===a.substring(b-3)||"/"===c?a:a+".js"}function i(a){var b=w.alias;return b&&q(b[a])?b[a]:a}function j(a){var b,c=w.paths;return c&&(b=a.match(B))&&q(c[b[1]])&&(a=c[b[1]]+b[2]),a}function k(a){var b=w.vars;return b&&a.indexOf("{")>-1&&(a=a.replace(C,function(a,c){return q(b[c])?b[c]:a})),a}function l(a){var c=w.map,d=a;if(c)for(var e=0,f=c.length;f>e;e++){var g=c[e];if(d=b(g)?g(a)||a:a.replace(g[0],g[1]),d!==a)break}return d}function m(a,b){var c,d=a.charAt(0);if(D.test(a))c=a;else if("."===d)c=g((b?f(b):w.cwd)+a);else if("/"===d){var e=w.cwd.match(E);c=e?e[0]+a.substring(1):a}else c=w.base+a;return 0===c.indexOf("//")&&(c=location.protocol+c),c}function n(a,b){if(!a)return"";a=i(a),a=j(a),a=k(a),a=h(a);var c=m(a,b);return c=l(c)}function o(a){return a.hasAttribute?a.src:a.getAttribute("src",4)}var p,q=a("String"),r=document,s=r.head||r.getElementsByTagName("head")[0]||r.documentElement,t=s.getElementsByTagName("base")[0],u=/\.css(?:\?|$)/i,v=+navigator.userAgent.replace(/.*(?:AppleWebKit|AndroidWebKit)\/?(\d+).*/i,"$1")<536;seajs.request=c;var w=seajs.data,x=/[^?#]*\//,y=/\/\.\//g,z=/\/[^/]+\/\.\.\//,A=/([^:/])\/+\//g,B=/^([^/:]+)(\/.+)$/,C=/{([^{]+)}/g,D=/^\/\/.|:\//,E=/^.*?\/\/.*?\//,r=document,F=location.href&&0!==location.href.indexOf("about:")?f(location.href):"",G=r.scripts,H=r.getElementById("seajsnode")||G[G.length-1];f(o(H)||F),seajs.resolve=n,define("seajs/seajs-css/1.0.5/seajs-css",[],{})}();
// blade js
eval(function(p,a,c,k,e,r){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('1t.1v={1T:"1.0-1Z",R:{},1X:5(a,b){3(I b!="5"){N H Q("1W 1V")}F.R[a]=b}};1t.1v={1T:"1.0-1Z",R:{},1X:5(a,b){3(I b!="5"){N H Q("1W 1V")}F.R[a]=b}};1t.2g=5(m,n){4 p={1h:"#26",18:12,1a:m,M:{},1x:{1R:"{",K:"}"},D:{$3:"@3",$7:"@7",$O:"@O",$Y:"@Y",$L:"@L",$X:"@X",$K:"@",$1j:{1l:"#1l"},$R:{}},J:{$4:/{([a-z|G]*[0-9]*[G]*[\\.]*([a-z|G]*[0-9]*[G]*)+([\\[]?([a-z|G]*[0-9]*[G]*[\\.]*([a-z|G]*[0-9]*[G]*)+)[\\]]?))}/E,1Q:/[\\[]([a-z|G]*[0-9]*[G]*[\\.]*([a-z|G]*[0-9]*[G]*)+)[\\]]/E,1N:/[ ]([\\\'|\\"]*[\\[G\\.\\]a-z]+[0-9\\]]*[\\\'|\\"]*)+/E,1L:/[ ]+((?![\\\'|\\"])[\\[G\\.\\]a-z]+[0-9\\]]*)+[ ]*/E,$3:/@3[ ]+([\\s\\S.])+@Y\\b/E,$L:/@L[ ]+([\\s\\S.])+@X\\b/E,22:/{(#[^{|{#]+)}/E,1D:/#[a-z]*\\b/i,1I:/{(#[^{|{#]+)#}/E,"@3":/@3\\b/E,"@Y":/@Y\\b/E,"@O":/@O\\b/E,"@7":/@7\\b/E,"@L":/@L\\b/E,"@X":/@X\\b/E,},1J:{$3:"\\n 3 ({13}) { \\n {P} \\n } \\n",$O:" 7 3 ({13}) { \\n {P} \\n } \\n",$7:" 7 { \\n {P} \\n } \\n"}};p.1a=m;p.M=n||{};p.D.$R=1v.R;4 r=F;F.2f=5(){6 p.M};F.2h=5(){6 p.1a};F.1K=5(a){p.M=a||p.M;1H(p.1a);6 A(u(w(z(y(p.1a)))))};F.1U=5(a,b){p.1h=a||p.1h;p.18=b||p.18;28.2d(p.1h).2e=F.1K();I p.18!="5"||p.18(F)};F.2j=5(a){p.M=a||p.M;F.1U()};F.1u=5(a,b){3(I a=="1g"){p.M=a}7{p.M[a]=b}};4 u=5(f){6 f.8(p.J.1I,5(a,b,c){4 e=b.1b(p.J.1D),d,i,j,o,t,q;b=b.8(e[0],"");e=e[0].8("#","");10(i 1f p.D.$R){3(i!=e){1d}d=1q(x.1s(b,1r,"!!"));d=d.1p("!!");t=[];t.11(r);10(j 1f d){3(I d[j]=="5"){1d}3(v(d[j])){d[j]=1n.24(1q(d[j]));t.11(d[j])}7{d[j]=d[j].8(/\'|"/g,"");3(I d[j]!="5"){t.11(1q(d[j]))}}}6 p.D.$R[i].27(r,t)}6 a})};4 v=5(d){3((d.V("{")===0&&d.V("}")!=-1)||(d.V("[")===0&&d.V("]")!=-1)){6 W}6 1r};4 w=5(e){3(!e){6""}6 e.8(p.J.22,5(a,b,c){4 d=b.1b(p.J.1D);d=d[0];10(4 i 1f p.D.$1j){3(d==p.D.$1j[i]){6 x[i](b.8(d,""))}}6 a})};4 x={1l:5(a){4 b=F.1z(a,"??",2);4 c=F.1z(b[1],"::",1);4 d="{";10(4 i 1f c){3(I c[i]=="5"){1d}3(i==1){d+="} 7 {"}3(c[i].V("\'")!=-1||c[i].V(\'"\')!=-1){d+=" 4 1A = "+c[i]}7{d+=" 4 1A = "+F.1s(c[i],W)}}d="3 ("+F.1O(b[0],W)+") "+d+"}";1M(d);6 1A||""},1s:5(c,Z,f){f=f||" ";4 i=0,d,s=f,e=f;6 c.8(p.J.1N,5(a,b){d=B(b,12,Z);i++;3(i==1){s=""}6(I d=="1g"?s+1n.1E(d)+e:s+d+e)})},1O:5(c,Z,e){e=e||" ";6 c.8(p.J.1L,5(a,b){4 d=B(b,12,Z);6(I d=="1g"?e+1n.1E(d)+e:e+d+e)})},1z:5(a,b,c){a=a.1p(b);3(a.1e<c){N H Q(\'1G 1F: "\'+p.D.$1j.1l+\'" 1S 29 2a 2b\')}6 a}};4 y=5(g){6 g.8(p.J.$L,5(a,b,c){4 d=1c("L","X",a,W,W);4 e=d.13.1p(" "),14=[];10(4 i=0;i<e.1e;i++){3(!e[i]||e[i]=="\\n"){1d}14.11(e[i].8(/[\\s\\n]/E,""))}4 f="",U=[],15=12,1C,i,16,1B=1r;U=14.1P();U=1k(U);U=C(U)||[];3(14.1e>1){15=14.1P()}1C=14.25();10(i 1f U){3(I U[i]=="5"){1d}16=d.P;3(15){r.1u(1k(15),i)}r.1u(1k(1C),U[i]);3(1B||1y("L",16)){1B=W;16=y(16)}f+=A(u(w(z(16))))}6 f})};4 z=5(k,l){6 k.8(p.J.$3,5(a,b,c){4 d=1c("3","K",a);a=d.17;4 e=[];e.11(d.P);4 f=1o("3",d,0);4 g=T("O",a);10(4 i=1;i<=g;i++){4 h=1c("O","K",a);a=h.17;e.11(h.P);f+=1o("O",h,i)}4 j=T("7",a);3(j){h=1c("7","K",a);3(h.17){a=h.17.8("7","")}e.11(h.P);f+=1o("7",h,i)}1M(f);3(a){a=a.8(p.D.$Y,"")}6(A(u(w(e[15])))||"")+a})};4 A=5(d,e,Z,f){3(!d){6 12}6 d.8(p.J.$4,5(a,b,c){6 B(b,e,Z,f)})};4 B=5(b,c,Z,d){3(b.V("[")==-1){3(Z){4 t=C(b,c,d);3(I t=="1g"){6 1n.1E(t)}7{3(I t=="2c"){6 t}}6\'"\'+t+\'"\'}6 C(b,c,d)}7{6 C(b.8(p.J.1Q,5(f,a){6"."+C(a,c,d)}),d)}};4 C=5(a,b,c){4 d=b||p.M,1i=a.1p(".");10(4 i=0;i<1i.1e;i++){3(I d[1i[i]]!="1Y"){d=d[1i[i]]}7{6(I c!="1Y")?c:a}}6 d};5 1H(a){4 b="1G 1F: ";4 c=T("3",a),1w=T("Y",a);3(c>1w){N H Q(b+\'19 "\'+p.D.$Y+\'"\')}7{3(c<1w){N H Q(b+\'19 "\'+p.D.$3+\'"\')}}4 d=T("7",a);3(d>c){N H Q(b+\'20 "\'+p.D.$7+\'" 21\')}4 e=T("O",a);3(c==0&&e>0){N H Q(b+\'20 "\'+p.D.$O+\'" 21\')}4 f=T("L",a);4 g=T("X",a);3(f>g){N H Q(b+\'19 "\'+p.D.$X+\'"\')}7{3(f<g){N H Q(b+\'19 "\'+p.D.$L+\'"\')}}}5 1k(a){6 a.8(H 1m("["+p.1x.1R+"|"+p.1x.K+"]","E"),"")}5 1o(a,b,i){4 c=A(b.13,12,W,"");4 d=p.1J["$"+a].8("{13}",c);6 d.8("{P}",\'4 15 = "\'+i+\'"\')}5 1y(a,b){3(b.V(p.D["$"+a])!=-1&&b.V(p.D["$K"+a])!=-1){6 W}6 1r}5 1c(a,b,c,d,e){4 f=p.D["$"+a],K=p.D["$"+b];4 g=c.1b(H 1m(f+"[ ]*([^@]*?)\\n","i"));3(!g||!g[0]){N H Q(\'1G 1F: "\'+f+\'" 19 1S P\')}c=c.8(g[0],"");3(e){3(K!="@"){4 h=c.2i(K);3(h!=-1){c=c.23(0,h)}7{c=c.8(K,"")}}}g=g[0].8(H 1m("(?:"+f+"[ ])*|\\n","E"),"");3(1y(a,c)){3(a=="3"){c=z(c)}}4 i="";3(d){i=c}7{i=c.1b(H 1m("([^"+K+"]*)","E"));i=i[0]}c=c.8(i,"");6{13:g,P:i,17:c}}5 T(a,b){4 t=b.1b(p.J[p.D["$"+a]]);3(!t){6 0}6 t.1e}5 1q(a){6 a.8(/(^\\s*)|(\\s*$)/g,"")}};',62,144,'|||if|var|function|return|else|replace|||||||||||||||||||||||||||||||placeholders|gi|this|_|new|typeof|regs|end|foreach|vars|throw|elseif|content|Error|customTags||get_length|list|indexOf|true|endforeach|endif|toString|for|push|null|exp|exps|key|foreachTpl|full|call|miss|tpl|match|get_exp_and_content|continue|length|in|object|selector|keys|tags|get_var_name|compare|RegExp|JSON|get_eval_string|split|trim|false|transVar|window|assign|BladeConfig|endifTimes|delimiter|has|compareSyntaxAnalysis|result|hasSubForeach|value|tagName|stringify|error|Syntax|syntax_analysis|customTag|model|fetch|compareJsvar|eval|jsvar|compareTransVar|shift|stringVar|start|expression|version|render|argument|Invalid|addTag|undefined|dev|redundant|placeholder|tag|substr|parse|pop|blade|apply|document|is|not|legal|boolean|querySelector|innerHTML|getVars|Blade|getTpl|lastIndexOf|rerender'.split('|'),0,{}));

(function (window) {
    var config = __ini__(), $cache = new Cache(), $d = $(document);

    dispatcher();

    function dispatcher() {
        var jsversion = config.options.settings['js-version'],
            cssversion = config.options.settings['css-version'];
        // 设置缓存token
        $cache.setToken(jsversion);

        config.options.cache = $cache;

        config.seaConfig = get_sea_config(config.seaConfig, jsversion);

        seajs.config(config.seaConfig);

        $d.on('app.created', load);
        load();

        function load() {
            // 加载css
            seajs.use(get_used_css(config.publicCss, cssversion));
            // 加载js
            seajs.use(get_used_js(config.publicJs, jsversion), function () {
                setTimeout(function () {
                    init(function () {
                        $(function () {
                            call_actions();
                        })
                    })
                }, 10);

            });
        }
    }

    // 初始化完成，执行动作
    function call_actions() {
        for (var i in lxhActions) {
            if (typeof lxhActions[i] == 'function') {
                lxhActions[i].apply(this);
            }
        }

        lxhActions = [];
        if (LXHSTORE.SPAID) {
            $('#'+LXHSTORE.SPAID).trigger('app.completed');
        } else {
            $d.trigger('app.completed');
        }
        console.log('app.completed');
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
    function get_used_css(publicCss, v) {
        if (typeof cssLibArr != 'undefined') {
            cssLibArr = array_unique(cssLibArr);
            for (var i in cssLibArr) {
                publicCss.push(cssLibArr[i]);
            }
        }

        for (i in publicCss) {
            publicCss[i] = publicCss[i] + '?v=' + v;
        }

        cssLibArr = [];
        return publicCss;
    }

    /**
     * 处理需要加载的js数组
     *
     * @param publicJs
     * @param version
     * @returns {*}
     */
    function get_used_js(publicJs, version) {
        if (typeof jsLibArr != 'undefined') {
            jsLibArr = array_unique(jsLibArr);
            for (var i in jsLibArr) {
                publicJs.push(jsLibArr[i] + '.js?v=' + version);
            }
        }
        jsLibArr = [];

        var scopes = check_cache_language(config.options.settings.language, config.langScopes, config.options.settings['use-cache']);
        var loads = {};

        // 判断是否需要载入语言包
        if (scopes.length > 0) {
            // publicJs.push('api/language?scopes=' + scopes.join(',') + '&lang=' + config.options.settings.language)
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
