import { Injectable } from '@angular/core';
import { CanActivate,   Router } from '@angular/router'; 
import { UserService } from '../common-services/user.service';

@Injectable({
  providedIn: 'root'
})
export class LoggedoutGuard implements CanActivate {
  constructor(private usrSer:UserService, private route:Router){}
  canActivate( ):  boolean   {
    let logged = this.usrSer.loggedIn();
    if(logged){  
      this.route.navigate(['/dashboard']);
     return false;
    }else{ 
      return true;
    }
  }
  
}
