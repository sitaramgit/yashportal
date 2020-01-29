import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { UserRoutingModule } from './user-routing.module';
import { UserComponent } from './user.component';
import { UserProfileComponent } from './user-profile/user-profile.component';
import { UserEditProfileComponent } from './user-edit-profile/user-edit-profile.component';
import { UserChangePasswordComponent } from './user-change-password/user-change-password.component';
import { FormsModule } from '@angular/forms';


@NgModule({
  declarations: [UserComponent, UserProfileComponent, UserEditProfileComponent, UserChangePasswordComponent],
  imports: [
    CommonModule,
    FormsModule,
    UserRoutingModule
  ],
  bootstrap: [UserComponent]
})
export class UserModule { }
