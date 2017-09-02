import { IApiService } from "./i-api-service";
import { UrlParameter } from "./urlParameter";
import { ILogger, HttpService, IHttpService } from "./../../utils/index";
/// base abstraction of api-service
export abstract class BaseApiService implements IApiService {
    /// fields
    protected baseUrl: string;
    protected controllerName: string;
    get controllerUrl(): string {
        return this.baseUrl + "/" + this.controllerName;
    }
    /// injected dependencies
    protected httpService: IHttpService;
    protected logger: ILogger;
    /// ctor
    constructor(
        httpService: IHttpService,
        logger: ILogger,
        controllerName: string) {
        this.httpService = httpService;
        this.logger = logger;
        this.baseUrl = "http://localhost:5000/api";
        this.controllerName = controllerName;
    }
    /// url methods
    createUrlWithMethodName(methodName: string): string {
        return this.createUrlWithMethodNameAndParams(methodName, null);
    }
    createUrlWithParams(...parameters: string[]): string {
        return this.createUrlWithMethodNameAndParams(
            null,
            this.createParametersString(parameters));
    }
    createUrlWithMethodNameAndParams(methodName: string, ...parameters: string[]): string {
        let url = this.baseUrl + "/"
            + this.controllerName
            + (methodName ? ("/" + methodName) : "")
            + ((parameters && parameters.length) ? ("/" + this.createParametersString(parameters)) : "");
        return url;
    }
    createUrlWithMethodNameAndUrlParams(methodName: string, ...parameters: UrlParameter[]): string {
        let result: string = this.baseUrl + "/"
            + this.controllerName
            + (methodName ? ("/" + methodName) : "")
            + ((parameters && parameters.length) ? ("?" + this.createUrlParametersString(parameters)) : "");
        return result;
    }
    /// helpers
    private createParametersString(parameters: string[]): string {
        return (parameters && parameters.length) ? parameters.join("/") : "";
    }
    private createUrlParametersString(parameters: UrlParameter[]): string {
        let result: string = "";
        if (parameters && parameters.length) {
            let strParams: string[] = [];
            parameters.forEach(
                function (value: UrlParameter, index: number, array: UrlParameter[]) {
                    strParams.push(value.asString());
                });
            result = strParams.join("&");
        }
        return result;
    }
}