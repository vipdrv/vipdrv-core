import { Routes, RouterModule } from '@angular/router';

import { InvitationsComponent } from './invitations.component';
import { ModuleWithProviders } from '@angular/core';

// noinspection TypeScriptValidateTypes
export const routes: Routes = [
    {
        path: '',
        component: InvitationsComponent,
        children: []
    }
];

export const routing: ModuleWithProviders = RouterModule.forChild(routes);