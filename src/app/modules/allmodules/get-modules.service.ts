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
    let params={module:"modules",view:"list", name : module, id:this.usrSer.currentUserDetails().id, sessionid:this.usrSer.currentUserDetails().sessionid};
    return this._http.post(this.usrSer.serverUrl,params);
  }

  getModuleDetail(name,id):Observable<any>{
    let params={module:"modules",view:"details", name : name, id:id, sessionid:this.usrSer.currentUserDetails().sessionid};
    return this._http.post(this.usrSer.serverUrl,params);
  }
}
