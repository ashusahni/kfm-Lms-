import { useState } from "react";
import { Link, useNavigate } from "react-router-dom";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { useAuth } from "@/context/AuthContext";
import { useConfig } from "@/context/ConfigContext";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "@/components/ui/card";
import { Label } from "@/components/ui/label";
import { Alert, AlertDescription } from "@/components/ui/alert";
import { AnimatedBackground } from "@/components/aceternity/AnimatedBackground";
import { motion } from "framer-motion";
import { cn } from "@/lib/utils";

const emailSchema = z
  .object({
    username: z.string().min(1, "Email is required").email("Invalid email"),
    password: z.string().min(6, "At least 6 characters"),
    password_confirmation: z.string(),
  })
  .refine((d) => d.password === d.password_confirmation, {
    message: "Passwords must match",
    path: ["password_confirmation"],
  });

const mobileSchema = z
  .object({
    country_code: z.string().min(1, "Country code is required"),
    mobile: z.string().min(1, "Mobile number is required").regex(/^[0-9]+$/, "Numbers only"),
    password: z.string().min(6, "At least 6 characters"),
    password_confirmation: z.string(),
  })
  .refine((d) => d.password === d.password_confirmation, {
    message: "Passwords must match",
    path: ["password_confirmation"],
  });

type EmailFormData = z.infer<typeof emailSchema>;
type MobileFormData = z.infer<typeof mobileSchema>;

type RegisterMode = "email" | "mobile";

const Register = () => {
  const navigate = useNavigate();
  const { registerStep, loading } = useAuth();
  const { appConfig, loading: configLoading } = useConfig();
  const [error, setError] = useState<string | null>(null);
  const [debugCode, setDebugCode] = useState<string | null>(null);
  const defaultMode: RegisterMode =
    appConfig?.register_method === "mobile" ? "mobile" : "email";
  const [mode, setMode] = useState<RegisterMode>(defaultMode);

  const emailForm = useForm<EmailFormData>({
    resolver: zodResolver(emailSchema),
    defaultValues: {
      username: "",
      password: "",
      password_confirmation: "",
    },
  });

  const mobileForm = useForm<MobileFormData>({
    resolver: zodResolver(mobileSchema),
    defaultValues: {
      country_code: "+91",
      mobile: "",
      password: "",
      password_confirmation: "",
    },
  });

  const handleResult = (
    result: { ok: boolean; message?: string; debugCode?: string }
  ) => {
    if (result.ok && !result.message) navigate("/onboarding", { replace: true });
    else if (result.ok && result.message) {
      setError(result.message);
      if (result.debugCode) setDebugCode(result.debugCode);
    } else setError(result.message ?? "Registration failed");
  };

  const onEmailSubmit = async (data: EmailFormData) => {
    setError(null);
    setDebugCode(null);
    const result = await registerStep(1, {
      email: data.username.trim(),
      password: data.password,
      password_confirmation: data.password_confirmation,
    });
    handleResult(result);
  };

  const onMobileSubmit = async (data: MobileFormData) => {
    setError(null);
    setDebugCode(null);
    const countryCode = data.country_code.replace(/\s/g, "").startsWith("+")
      ? data.country_code.trim()
      : `+${data.country_code.trim()}`;
    const result = await registerStep(1, {
      mobile: data.mobile.replace(/\D/g, ""),
      country_code: countryCode,
      password: data.password,
      password_confirmation: data.password_confirmation,
    });
    handleResult(result);
  };

  const renderError = () => {
    if (!error) return null;
    if (error.toLowerCase().includes("verify")) {
      return (
        <Alert>
          <AlertDescription className="space-y-2">
            <span className="block">{error}</span>
            {debugCode && (
              <span className="block text-sm font-mono font-semibold bg-muted px-2 py-1 rounded">
                Your verification code: {debugCode}
              </span>
            )}
            <span className="block text-sm text-muted-foreground">
              Check your spam folder if you don&apos;t see the email. Then use the link below to enter your code or sign in.
            </span>
            <div className="flex flex-wrap gap-2 mt-2">
              <Link to="/verify" className="font-medium underline">
                Enter verification code
              </Link>
              <span className="text-muted-foreground">·</span>
              <Link to="/login" className="font-medium underline">
                Sign in
              </Link>
            </div>
          </AlertDescription>
        </Alert>
      );
    }
    return (
      <Alert variant="destructive">
        <AlertDescription>{error}</AlertDescription>
      </Alert>
    );
  };

  return (
    <AnimatedBackground variant="grid">
      <motion.div
        initial={{ opacity: 0, y: 12 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.4 }}
        className="w-full max-w-md px-4"
      >
        <Card className="w-full border-border/80 bg-card/90 shadow-elevated backdrop-blur-md">
          <CardHeader className="space-y-1 text-center">
            <CardTitle className="text-2xl font-display">Create account</CardTitle>
            <CardDescription>
              Register with email or mobile number and password
            </CardDescription>
          </CardHeader>
          <CardContent>
            {configLoading && (
              <p className="text-sm text-muted-foreground text-center py-4">
                Loading registration options…
              </p>
            )}
            {!configLoading && (
              <>
                <div className="flex rounded-lg border border-border bg-muted/50 p-1 mb-4">
                  <button
                    type="button"
                    onClick={() => setMode("email")}
                    className={cn(
                      "flex-1 rounded-md py-2 text-sm font-medium transition-colors",
                      mode === "email"
                        ? "bg-background text-foreground shadow-sm"
                        : "text-muted-foreground hover:text-foreground"
                    )}
                  >
                    Email
                  </button>
                  <button
                    type="button"
                    onClick={() => setMode("mobile")}
                    className={cn(
                      "flex-1 rounded-md py-2 text-sm font-medium transition-colors",
                      mode === "mobile"
                        ? "bg-background text-foreground shadow-sm"
                        : "text-muted-foreground hover:text-foreground"
                    )}
                  >
                    Mobile
                  </button>
                </div>

                {mode === "email" ? (
                  <form onSubmit={emailForm.handleSubmit(onEmailSubmit)} className="space-y-4">
                    {renderError()}
                    <div className="space-y-2">
                      <Label htmlFor="username">Email</Label>
                      <Input
                        id="username"
                        type="email"
                        placeholder="you@example.com"
                        autoComplete="email"
                        className="transition-shadow focus-visible:ring-2"
                        {...emailForm.register("username")}
                      />
                      {emailForm.formState.errors.username && (
                        <p className="text-sm text-destructive">
                          {emailForm.formState.errors.username.message}
                        </p>
                      )}
                    </div>
                    <div className="space-y-2">
                      <Label htmlFor="password">Password</Label>
                      <Input
                        id="password"
                        type="password"
                        autoComplete="new-password"
                        className="transition-shadow focus-visible:ring-2"
                        {...emailForm.register("password")}
                      />
                      {emailForm.formState.errors.password && (
                        <p className="text-sm text-destructive">
                          {emailForm.formState.errors.password.message}
                        </p>
                      )}
                    </div>
                    <div className="space-y-2">
                      <Label htmlFor="password_confirmation">Confirm password</Label>
                      <Input
                        id="password_confirmation"
                        type="password"
                        autoComplete="new-password"
                        className="transition-shadow focus-visible:ring-2"
                        {...emailForm.register("password_confirmation")}
                      />
                      {emailForm.formState.errors.password_confirmation && (
                        <p className="text-sm text-destructive">
                          {emailForm.formState.errors.password_confirmation.message}
                        </p>
                      )}
                    </div>
                    <Button
                      type="submit"
                      className="w-full bg-gradient-cta text-primary-foreground shadow-md hover:opacity-90 transition-opacity"
                      disabled={loading}
                    >
                      {loading ? "Creating account…" : "Register"}
                    </Button>
                  </form>
                ) : (
                  <form onSubmit={mobileForm.handleSubmit(onMobileSubmit)} className="space-y-4">
                    {renderError()}
                    <div className="grid grid-cols-[1fr_2fr] gap-2">
                      <div className="space-y-2">
                        <Label htmlFor="country_code">Country code</Label>
                        <Input
                          id="country_code"
                          type="text"
                          placeholder="+91"
                          autoComplete="tel-country-code"
                          className="transition-shadow focus-visible:ring-2"
                          {...mobileForm.register("country_code")}
                        />
                        {mobileForm.formState.errors.country_code && (
                          <p className="text-sm text-destructive">
                            {mobileForm.formState.errors.country_code.message}
                          </p>
                        )}
                      </div>
                      <div className="space-y-2">
                        <Label htmlFor="mobile">Mobile number</Label>
                        <Input
                          id="mobile"
                          type="tel"
                          placeholder="9876543210"
                          autoComplete="tel-national"
                          className="transition-shadow focus-visible:ring-2"
                          {...mobileForm.register("mobile")}
                        />
                        {mobileForm.formState.errors.mobile && (
                          <p className="text-sm text-destructive">
                            {mobileForm.formState.errors.mobile.message}
                          </p>
                        )}
                      </div>
                    </div>
                    <div className="space-y-2">
                      <Label htmlFor="mobile-password">Password</Label>
                      <Input
                        id="mobile-password"
                        type="password"
                        autoComplete="new-password"
                        className="transition-shadow focus-visible:ring-2"
                        {...mobileForm.register("password")}
                      />
                      {mobileForm.formState.errors.password && (
                        <p className="text-sm text-destructive">
                          {mobileForm.formState.errors.password.message}
                        </p>
                      )}
                    </div>
                    <div className="space-y-2">
                      <Label htmlFor="mobile-password_confirmation">Confirm password</Label>
                      <Input
                        id="mobile-password_confirmation"
                        type="password"
                        autoComplete="new-password"
                        className="transition-shadow focus-visible:ring-2"
                        {...mobileForm.register("password_confirmation")}
                      />
                      {mobileForm.formState.errors.password_confirmation && (
                        <p className="text-sm text-destructive">
                          {mobileForm.formState.errors.password_confirmation.message}
                        </p>
                      )}
                    </div>
                    <Button
                      type="submit"
                      className="w-full bg-gradient-cta text-primary-foreground shadow-md hover:opacity-90 transition-opacity"
                      disabled={loading}
                    >
                      {loading ? "Creating account…" : "Register"}
                    </Button>
                  </form>
                )}
              </>
            )}
            <p className="mt-4 text-center text-sm text-muted-foreground">
              Already have an account?{" "}
              <Link to="/login" className="text-primary font-medium hover:underline">
                Sign in
              </Link>
            </p>
          </CardContent>
        </Card>
      </motion.div>
    </AnimatedBackground>
  );
};

export default Register;
