import { Routes, RouterModule }  from '@angular/router';

import { LogoutComponent } from './logout.component';
import { ModuleWithProviders } from '@angular/core';

// noinspection TypeScriptValidateTypes
export const routes: Routes = [
    {
        path: '',
        component: LogoutComponent,
        children: []
    }
];

export const routing: ModuleWithProviders = RouterModule.forChild(routes);