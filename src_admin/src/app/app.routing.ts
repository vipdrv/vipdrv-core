import { Routes, RouterModule } from '@angular/router';
import { ModuleWithProviders } from '@angular/core';
import { AuthorizationService } from './services/index';

export const routes: Routes = [
  { path: '', redirectTo: 'pages', pathMatch: 'full' },
  { path: '**', redirectTo: 'pages/home', canActivate: [AuthorizationService] },
];

export const routing: ModuleWithProviders = RouterModule.forRoot(routes, { useHash: true });