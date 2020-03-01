import { Routes, RouterModule } from '@angular/router';
import { ModuleWithProviders } from '@angular/core';

import { UserProfileComponent } from './userProfile.component';
// noinspection TypeScriptValidateTypes
export const routes: Routes = [
    {
        path: '',
        component: UserProfileComponent,
        children: []
    }
];
export const routing: ModuleWithProviders = RouterModule.forChild(routes);