import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { AppTranslationModule } from '../../app.translation.module';
import { NgaModule } from '../../theme/nga.module';

import { Ng2Bs3ModalModule } from 'ng2-bs3-modal/ng2-bs3-modal';

import { UtilsModule } from './../../utils/index';
import { BeveragesTableComponent } from './table/beveragesTable.component';

import { BeveragesComponent } from './beverages.component';
import { routing } from './beverages.routing';

@NgModule({
    imports: [
        CommonModule,
        FormsModule,
        AppTranslationModule,
        NgaModule,
        routing,
        UtilsModule,
        Ng2Bs3ModalModule
    ],
    exports: [
        BeveragesComponent,
        BeveragesTableComponent
    ],
    declarations: [
        BeveragesComponent,
        BeveragesTableComponent
    ],
    providers: []
})
export class BeveragesModule {}
