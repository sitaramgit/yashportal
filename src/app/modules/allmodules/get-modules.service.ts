import { Injectable } from '@angular/core';
import { UserService } from 'src/app/common-services/user.service';
import { Observable } from 'rxjs';
import { HttpClient } from '@angular/common/http';

@Injectable({
  providedIn: 'root'
})
export class GetModulesService {

  constructor(public usrSer:UserService,public _http:HttpClient) { }

  getModuleList(module):Observable<any>{
    // let params={module:"modules",view:"list", name : module, id:this.usrSer.currentUserDetails().id, sessionid:this.usrSer.currentUserDetails().sessionid};
    let params={module:module,view:"list", name : module, id:this.usrSer.currentUserDetails().id, sessionid:this.usrSer.currentUserDetails().sessionid};
    return this._http.post(this.usrSer.serverUrl,params);
  }

  getModuleDetail(name,id):Observable<any>{
    let params={module:name,view:"details", name : name, id:id, sessionid:this.usrSer.currentUserDetails().sessionid, customerid:this.usrSer.currentUserDetails().id};
    return this._http.post(this.usrSer.serverUrl,params);
  }

  createComment(name,id,cmt):Observable<any>{
    let params={module:name,view:"Comments", comments : cmt, id:id, sessionid:this.usrSer.currentUserDetails().sessionid, customerid:this.usrSer.currentUserDetails().id};
    return this._http.post(this.usrSer.serverUrl,params);
  }
}
