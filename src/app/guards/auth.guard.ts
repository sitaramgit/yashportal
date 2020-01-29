import { Injectable } from '@angular/core';
import { CanActivate, Router} from '@angular/router';
import { Observable } from 'rxjs';
import { UserService } from '../common-services/user.service';

@Injectable({
  providedIn: 'root'
})
export class AuthGuard implements CanActivate {
  constructor(private usrSer:UserService, private route:Router){}
  canActivate( ) :  boolean{
    let logged = this.usrSer.loggedIn();
    console.log(logged);
    if(logged){
    //  this.usrSer.session_id();
     return true;
    }else{
     this.route.navigate(['/login']);
      return false;
    }
  }
  
}
