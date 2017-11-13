import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpXService } from './httpX.service';
@NgModule({
    imports: [
        CommonModule
    ],
    declarations: [],
    exports: [],
    providers: [
        HttpXService
    ]
})
export class HttpXModule {}
