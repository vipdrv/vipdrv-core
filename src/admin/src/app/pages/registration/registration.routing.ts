import { Routes, RouterModule } from '@angular/router';

import { RegistrationComponent } from './registration.component';
import { ModuleWithProviders } from '@angular/core';

// noinspection TypeScriptValidateTypes
export const routes: Routes = [
    {
        path: '',
        component: RegistrationComponent,
        children: []
    },
    {
        path: ':invitationCode',
        component: RegistrationComponent,
        children: []
    },
];

export const routing: ModuleWithProviders = RouterModule.forChild(routes);