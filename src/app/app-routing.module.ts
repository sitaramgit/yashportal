import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { LoginComponent } from './common-components/login/login.component';
import { RegisterComponent } from './common-components/register/register.component';
import { ForgotPasswordComponent } from './common-components/forgot-password/forgot-password.component';
import { DashboardComponent } from './common-components/dashboard/dashboard.component';
import { PageNotFoundComponent } from './common-components/page-not-found/page-not-found.component';
import { AuthGuard } from './guards/auth.guard';
import { LoggedoutGuard } from './guards/loggedout.guard';


const routes: Routes = [
  {path:"", component:DashboardComponent, canActivate:[AuthGuard]},
  {path:"login", component:LoginComponent , canActivate:[LoggedoutGuard]},
  {path:"register", component:RegisterComponent, canActivate:[LoggedoutGuard]},
  {path:"forgot-password", component:ForgotPasswordComponent, canActivate:[LoggedoutGuard]},
  {path:"dashboard", component:DashboardComponent, canActivate:[AuthGuard]},
  {path:"user", loadChildren :() => import('./modules/user/user.module').then(m => m.UserModule), canActivate:[AuthGuard]},
  {path:"all-modules", loadChildren :() => import('./modules/allmodules/allmodules.module').then(m => m.AllmodulesModule), canActivate:[AuthGuard]},
  {path:"**", component:PageNotFoundComponent},
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
